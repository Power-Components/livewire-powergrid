<?php

namespace PowerComponents\LivewirePowerGrid;

use Exception;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\View\{Factory, View};
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Foundation\Application;
use Illuminate\Pagination\{LengthAwarePaginator, Paginator};
use Illuminate\Support\{Collection as BaseCollection, Facades\Cache, Facades\DB};
use Livewire\{Attributes\Computed, Component, WithPagination};
use PowerComponents\LivewirePowerGrid\DataSource\ProcessDataSource;
use PowerComponents\LivewirePowerGrid\DataSource\Processors\{DataSourceBase};
use PowerComponents\LivewirePowerGrid\Events\PowerGridPerformanceData;
use PowerComponents\LivewirePowerGrid\Exceptions\TableNameCannotCalledDefault;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * @property-read mixed $getRecords
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
    use Concerns\LazyManager;
    use Concerns\Listeners;
    use Concerns\ManageRow;
    use Concerns\Persist;
    use Concerns\Radio;
    use Concerns\SoftDeletes;
    use Concerns\Sorting;
    use Concerns\Summarize;
    use Concerns\ToggleDetail;
    use WithPagination;

    public array $theme = [];

    /**
     * @throws TableNameCannotCalledDefault
     * @throws Exception|InvalidArgumentException
     */
    public function mount(): void
    {
        $themeClass = $this->customThemeClass() ?? strval(config('livewire-powergrid.theme'));

        $this->theme = app($themeClass)->apply();

        $this->prepareActionsResources();
        $this->prepareRowTemplates();

        $this->readyToLoad = !$this->deferLoading;

        foreach ($this->setUp() as $setUp) {
            $this->setUp[$setUp->name] = $setUp;
        }

        $this->throwTableName();
        $this->throwColumnAction();

        $this->columns = $this->columns();

        $this->restoreState();

        $this->resolveSummarizeColumn();
    }

    public function hydrate(): void
    {
        DataSourceBase::$actionsHtml = [];
    }

    public function fetchDatasource(): void
    {
        $this->readyToLoad = true;
    }

    public function updatedPage(): void
    {
        $this->checkboxAll = false;

        if (!app()->runningInConsole() && $this->hasLazyEnabled) {
            $this->additionalCacheKey = uniqid();

            data_set($this->setUp, 'lazy.items', 0);

            $this->render();

            $this->dispatch('pg:scrollTop', name: $this->getName());
        }
    }

    public function updatedSearch(): void
    {
        $this->gotoPage(1, data_get($this->setUp, 'footer.pageName'));

        if (!app()->runningInConsole() && $this->hasLazyEnabled) {
            $this->additionalCacheKey = uniqid();

            data_set($this->setUp, 'lazy.items', 0);
        }
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
    protected function getRecords(): mixed
    {
        if (!$this->readyToLoad) {
            return collect();
        }

        if (filled(data_get($this->setUp, 'cache.enabled')) && Cache::supportsTags()) {
            return $this->getRecordsFromCache();
        }

        return $this->getRecordsDataSource();
    }

    private function getRecordsFromCache(): mixed
    {
        $prefix    = strval(data_get($this->setUp, 'cache.prefix'));
        $customTag = strval(data_get($this->setUp, 'cache.tag'));
        $ttl       = intval(data_get($this->setUp, 'cache.ttl'));

        $tag      = $prefix . ($customTag ?: 'powergrid-' . $this->datasource()->getModel()->getTable() . '-' . $this->tableName);
        $cacheKey = implode('-', $this->getCacheKeys());

        $results = Cache::tags($tag)->remember($cacheKey, $ttl, fn () => ProcessDataSource::make($this)->get());

        if ($this->measurePerformance) {
            app(Dispatcher::class)->dispatch(
                new PowerGridPerformanceData(
                    tableName: $this->tableName,
                    retrieveDataInMs: 0,
                    isCached: true,
                )
            );
        }

        return $results;
    }

    private function getRecordsDataSource(): Paginator|MorphToMany|\Illuminate\Contracts\Pagination\LengthAwarePaginator|LengthAwarePaginator|BaseCollection
    {
        if ($this->measurePerformance) {
            DB::enableQueryLog();
        }

        $start        = microtime(true);
        $results      = ProcessDataSource::make($this)->get();
        $retrieveData = round((microtime(true) - $start) * 1000);

        if ($this->measurePerformance) {
            $queries = DB::getQueryLog();

            DB::disableQueryLog();

            /** @var float $queriesTime */
            $queriesTime = collect($queries)->sum('time');

            app(Dispatcher::class)->dispatch(
                new PowerGridPerformanceData(
                    $this->tableName,
                    retrieveDataInMs: $retrieveData,
                    transformDataInMs: app(DataSourceBase::class)->transformTime(),
                    queriesTimeInMs: $queriesTime,
                    queries: $queries,
                )
            );
        }

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
                    'table'       => 'livewire-powergrid::components.table',
                    'data'        => [],
                ]
            )->render();
        }

        return "<span>{$noDataLabel}</span>";
    }

    public function noDataLabel(): string|View
    {
        return view('livewire-powergrid::components.table.no-data-label');
    }

    public function getPublicPropertiesDefinedInComponent(): array
    {
        return collect((new \ReflectionClass($this))->getProperties(\ReflectionProperty::IS_PUBLIC)) // @phpstan-ignore-line
            ->where('class', get_class($this))
            ->pluck('name')
            ->intersect(array_keys($this->all()))
            ->mapWithKeys(fn ($property) => [$property => $this->$property])
            ->all();
    }

    public function render(): Application|Factory|View
    {
        if (isset($this->setUp['lazy'])) {
            $cacheKey = 'lazy-tmp-' . $this->getId() . '-' . implode('-', $this->getCacheKeys());

            $data = Cache::remember($cacheKey, 60, fn () => $this->getRecords());

            /** @phpstan-ignore-next-line */
            $this->totalCurrentPage = method_exists($data, 'items') ? count($data->items()) : $data->count();
        } else {
            $data = $this->getRecords();
        }

        $this->storeActionsRowInJSWindow();

        $this->storeActionsHeaderInJSWindow();

        $this->resolveDetailRow($data);

        $this->resolveFilters();

        return view(theme_style($this->theme, 'layout.table'), [
            'data'  => $data,
            'table' => 'livewire-powergrid::components.table',
        ]);
    }
}
