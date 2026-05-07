<?php

namespace PowerComponents\LivewirePowerGrid;

use Exception;
use Illuminate\Contracts\View\{Factory, View};
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Foundation\Application;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Pagination\{LengthAwarePaginator, Paginator};
use Illuminate\Support\Collection;
use Illuminate\Support\{Collection as BaseCollection, Facades\Cache};
use Livewire\{Attributes\Computed, Component, WithPagination};
use PowerComponents\LivewirePowerGrid\DataSource\ProcessDataSource;
use PowerComponents\LivewirePowerGrid\Exceptions\TableNameCannotCalledDefault;
use PowerComponents\LivewirePowerGrid\Themes\Theme;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * @property-read mixed $records
 * @property-read bool $hasColumnFilters
 * @property-read array|BaseCollection $visibleColumns
 * @property-read string $realPrimaryKey
 */
class PowerGridComponent extends Component
{
    use Concerns\Base;
    use Concerns\Checkbox;
    use Concerns\Filter;
    use Concerns\HasActions;
    use Concerns\Hooks;
    use Concerns\Listeners;
    use Concerns\ManageRow;
    use Concerns\Persist;
    use Concerns\Radio;
    use Concerns\SoftDeletes;
    use Concerns\Sorting;
    use Concerns\Summarize;
    use WithPagination;

    public function template(): ?Theme
    {
        return null;
    }

    public function boot(): void
    {
        $themeClass = $this->customThemeClass() ?? strval(config('livewire-powergrid.theme'));

        /** @var Theme $themeInstance */
        $themeInstance = app($themeClass);

        $customTheme = $this->template();

        if ($customTheme instanceof Theme) {
            $themeInstance = $customTheme;
        }

        app()->instance('powergrid.theme', $themeInstance);
    }

    /**
     * @throws TableNameCannotCalledDefault
     * @throws Exception|InvalidArgumentException
     */
    public function mount(): void
    {
        $this->prepareRowTemplates();

        $this->readyToLoad = ! $this->deferLoading;

        foreach ($this->setUp() as $setUp) {
            $this->setUp[$setUp->name] = $setUp;
        }

        $this->throwTableName();
        $this->throwColumnAction();

        $this->columns = $this->columns();

        $this->restoreState();

        $this->applyDefaultFilters();

        $this->resolveSummarizeColumn();
    }

    public function fetchDatasource(): void
    {
        $this->readyToLoad = true;
    }

    public function updatedPage(): void
    {
        $this->checkboxAll = false;
    }

    public function updatedSearch(): void
    {
        $this->gotoPage(1, data_get($this->setUp, 'footer.pageName'));
    }

    #[Computed]
    public function hasColumnFilters(): bool
    {
        return collect($this->columns)
            ->filter(fn ($column) => filled(data_get($column, 'filters')))->count() > 0;
    }

    #[Computed]
    public function visibleColumns(): BaseCollection
    {
        return collect($this->columns)
            ->where('forceHidden', false)
            ->map(function ($column) {
                data_forget($column, 'rawQueries');

                return $column;
            });
    }

    #[Computed]
    protected function records(): mixed
    {
        if (! $this->readyToLoad) {
            return collect();
        }

        if (filled(data_get($this->setUp, 'cache.enabled')) && Cache::supportsTags()) {
            return $this->getRecordsFromCache();
        }

        return $this->getRecordsDataSource();
    }

    private function getRecordsFromCache(): mixed
    {
        $prefix = strval(data_get($this->setUp, 'cache.prefix'));
        $customTag = strval(data_get($this->setUp, 'cache.tag'));
        $ttl = intval(data_get($this->setUp, 'cache.ttl'));

        $tag = $prefix.($customTag ?: 'powergrid-'.$this->datasource()->getModel()->getTable().'-'.$this->tableName);
        $cacheKey = implode('-', $this->getCacheKeys());

        /** @var array $results */
        $results = Cache::tags($tag)->remember($cacheKey, $ttl, fn () => ProcessDataSource::make($this)->get());

        $results['actionsByRow'] = $this->transformActions($results['actionsByRow'], $results['results']->getCollection());

        $this->js('pgActions', json_encode($results['actionsByRow']));

        return $this->applyAfterQuery($results['results']);
    }

    private function getRecordsDataSource(): Paginator|MorphToMany|\Illuminate\Contracts\Pagination\LengthAwarePaginator|LengthAwarePaginator|BaseCollection
    {
        $processResult = ProcessDataSource::make($this)->get();

        /** @var BaseCollection $actionsRows */
        $actionsRows = ($processResult['results'] instanceof AbstractPaginator || $processResult['results'] instanceof \Illuminate\Contracts\Pagination\Paginator)
            ? $processResult['results']->getCollection()
            : new BaseCollection($processResult['results']);

        $processResult['actionsByRow'] = $this->transformActions($processResult['actionsByRow'], $actionsRows);

        $this->js('pgActions', json_encode($processResult['actionsByRow']));

        return $this->applyAfterQuery($processResult['results']);
    }

    private function applyAfterQuery(mixed $results): Paginator|MorphToMany|LengthAwarePaginator|\Illuminate\Contracts\Pagination\LengthAwarePaginator|BaseCollection
    {
        if ($results instanceof AbstractPaginator || $results instanceof \Illuminate\Contracts\Pagination\Paginator) {
            /** @var Paginator|LengthAwarePaginator|\Illuminate\Contracts\Pagination\LengthAwarePaginator $results */
            $results->setCollection($this->transformRows($results->getCollection()));

            return $results;
        }

        if ($results instanceof BaseCollection) {
            return $this->transformRows($results);
        }

        /** @var MorphToMany|Collection $results */
        return $results;
    }

    protected function getCacheKeys(): array
    {
        return [
            json_encode(['page' => $this->getPage()]),
            json_encode(['perPage' => data_get($this->setUp, 'footer.perPage')]),
            json_encode(['search' => $this->search]),
            json_encode(['sortDirection' => $this->sortDirection]),
            json_encode(['sortField' => $this->sortField]),
            json_encode(['filters' => $this->filters]),
            json_encode(['sortArray' => $this->sortArray]),
        ];
    }

    /**
     * @throws Exception
     */
    private function throwColumnAction(): void
    {
        $hasColumnAction = collect($this->columns())
            ->filter(fn ($column) => data_get($column, 'isAction') === true)
            ->isEmpty();

        if ($hasColumnAction && method_exists(get_called_class(), 'actions')) {
            throw new Exception('To display \'actions\' you must define `Column::action(\'Action\')` in the columns method');
        }
    }

    /**
     * @throws TableNameCannotCalledDefault
     */
    private function throwTableName(): void
    {
        if (blank($this->tableName) || $this->tableName === 'default') {
            throw new TableNameCannotCalledDefault();
        }
    }

    #[Computed]
    public function processNoDataLabel(): string
    {
        $noDataLabel = $this->noDataLabel();

        if ($noDataLabel instanceof View) {
            return $noDataLabel->with(
                [
                    'noDataLabel' => trans('livewire-powergrid::datatable.labels.no_data'),
                    'table' => 'livewire-powergrid::components.table',
                    'data' => [],
                ]
            )->render();
        }

        return "<span>{$noDataLabel}</span>";
    }

    public function noDataLabel(): string|View
    {
        return view(theme_view('table.no-data-label'));
    }

    public function getPublicPropertiesDefinedInComponent(): array
    {
        return collect((new \ReflectionClass($this))->getProperties(\ReflectionProperty::IS_PUBLIC))
            ->where('class', get_class($this))
            ->pluck('name')
            ->intersect(array_keys($this->all()))
            ->mapWithKeys(fn ($property) => [$property => $this->$property])
            ->all();
    }

    public function toggleDetail(string $rowId): void
    {
        $this->dispatch('pg-toggle-detail-'.$this->tableName.'-'.$rowId, collapsed: null);
    }

    #[Computed]
    public function total(): ?int
    {
        if (method_exists($this->records, 'total')) {
            return $this->records->total();
        }

        if (method_exists($this->records, 'count')) {
            return $this->records->count();
        }

        if (is_countable($this->records)) {
            return count($this->records);
        }

        return 0;
    }

    public function render(): Application|Factory|View
    {
        $this->resolveFilters();

        return view(theme_view('table'));
    }
}
