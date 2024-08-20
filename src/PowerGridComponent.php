<?php

namespace PowerComponents\LivewirePowerGrid;

use Exception;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\{Factory, View};
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Pagination\{LengthAwarePaginator, Paginator};
use Illuminate\Support\{Collection as BaseCollection, Facades\Cache, Facades\DB};

use Livewire\{Attributes\Computed, Component, WithPagination};

use PowerComponents\LivewirePowerGrid\DataSource\Processors\{DataSourceBase};
use PowerComponents\LivewirePowerGrid\Events\PowerGridPerformanceData;

use Throwable;

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
    use Concerns\Radio;
    use Concerns\Filter;
    use Concerns\HasActions;
    use Concerns\Hooks;
    use Concerns\LazyManager;
    use Concerns\Listeners;
    use Concerns\Persist;
    use Concerns\SoftDeletes;
    use Concerns\Sorting;
    use Concerns\Summarize;
    use Concerns\ToggleDetail;
    use Concerns\ManageRow;
    use WithPagination;

    public function mount(): void
    {
        $this->start();

        $themeClass = $this->customThemeClass() ?? strval(config('livewire-powergrid.theme'));

        $this->themeRoot = app($themeClass)->root();

        $this->prepareActionsResources();
        $this->prepareRowTemplates();

        $this->readyToLoad = !$this->deferLoading;

        foreach ($this->setUp() as $setUp) {
            $this->setUp[$setUp->name] = $setUp;
        }

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

        if ($this->hasLazyEnabled) {
            $this->additionalCacheKey = uniqid();

            data_set($this->setUp, 'lazy.items', 0);

            $this->render();

            $this->dispatch('pg:scrollTop', name: $this->getName());
        }
    }

    public function updatedSearch(): void
    {
        $this->gotoPage(1);

        if ($this->hasLazyEnabled) {
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
            ->where('forceHidden', false);
    }

    #[Computed]
    protected function getRecords(): mixed
    {
        if (!$this->readyToLoad) {
            return collect();
        }

        $start = microtime(true);

        if (boolval(data_get($this->setUp, 'cache.enabled')) && Cache::supportsTags()) {
            return $this->getRecordsFromCache($start);
        }

        return $this->getRecordsDataSource($start);
    }

    private function getRecordsFromCache(float $start): mixed
    {
        $prefix    = strval(data_get($this->setUp, 'cache.prefix'));
        $customTag = strval(data_get($this->setUp, 'cache.tag'));
        $ttl       = intval(data_get($this->setUp, 'cache.ttl'));

        $tag      = $prefix . ($customTag ?: 'powergrid-' . $this->datasource()->getModel()->getTable() . '-' . $this->tableName);
        $cacheKey = implode('-', $this->getCacheKeys());

        $results = Cache::tags($tag)->remember($cacheKey, $ttl, fn () => ProcessDataSource::make($this)->get());

        if ($this->measurePerformance) {
            $time = round((microtime(true) - $start) * 1000);

            app(Dispatcher::class)->dispatch(
                new PowerGridPerformanceData(
                    tableName: $this->tableName,
                    retrieveDataInMs: $time,
                    isCached: true,
                )
            );
        }

        return $results;
    }

    private function getRecordsDataSource(float $start): Paginator|MorphToMany|\Illuminate\Contracts\Pagination\LengthAwarePaginator|LengthAwarePaginator|BaseCollection
    {
        if ($this->measurePerformance) {
            DB::enableQueryLog();
        }

        $results = ProcessDataSource::make($this)->get();

        if ($this->measurePerformance) {
            $queries = DB::getQueryLog();

            DB::disableQueryLog();

            $retrieveData = round((microtime(true) - $start) * 1000);

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

    private function throwColumnAction(): void
    {
        $hasColumnAction = collect($this->columns())
            ->filter(fn ($column) => data_get($column, 'isAction') === true)
            ->isEmpty();

        if ($hasColumnAction && method_exists(get_called_class(), 'actions')) {
            throw new Exception('To display \'actions\' you must define `Column::action(\'Action\')` in the columns method');
        }
    }

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

    private function renderView(mixed $data): Application|Factory|View
    {
        $themeClass = $this->customThemeClass() ?? strval(config('livewire-powergrid.theme'));

        $theme = app($themeClass)->apply();

        return view(theme_style($theme, 'layout.table'), [
            'data'  => $data,
            'theme' => $theme,
            'table' => 'livewire-powergrid::components.table',
        ]);
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

    /**
     * @throws Exception|Throwable
     */
    public function render(): Application|Factory|View
    {
        $data = $this->getRecords();

        $this->storeActionsRowInJSWindow();

        $this->storeActionsHeaderInJSWindow();

        if (empty(data_get($this->setUp, 'lazy'))) {
            $this->resolveDetailRow($data);
        }

        /** @phpstan-ignore-next-line */
        $this->totalCurrentPage = method_exists($data, 'items') ? count($data->items()) : $data->count();

        $this->resolveFilters();

        return $this->renderView($data);
    }
}
