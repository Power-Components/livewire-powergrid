<?php

namespace PowerComponents\LivewirePowerGrid;

use Exception;
use Illuminate\Contracts\View\{Factory, View};
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Foundation\Application;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\{Collection as BaseCollection, Facades\Cache};
use Livewire\{Attributes\Computed, Component, WithPagination};
use PowerComponents\LivewirePowerGrid\DataSource\ProcessDataSource;
use PowerComponents\LivewirePowerGrid\Exceptions\TableNameCannotCalledDefault;
use PowerComponents\LivewirePowerGrid\Plugins\PluginBase;
use PowerComponents\LivewirePowerGrid\Support\ThemeManager;
use PowerComponents\LivewirePowerGrid\Themes\Theme;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * @property-read mixed $records
 * @property-read bool $hasColumnFilters
 * @property-read array<int, Column>|BaseCollection<int, Column> $visibleColumns
 * @property-read string $realPrimaryKey
 *
 * @method mixed datasource(mixed ...$args)
 * @method mixed actions(mixed $row)
 */
class PowerGridComponent extends Component
{
    use Concerns\Base;
    use Concerns\Checkbox;
    use Concerns\Filter;
    use Concerns\HasActions;
    use Concerns\HasExport;
    use Concerns\Hooks;
    use Concerns\Listeners;
    use Concerns\ManageRow;
    use Concerns\Persist;
    use Concerns\Radio;
    use Concerns\SoftDeletes;
    use Concerns\Sorting;
    use Concerns\Summarize;
    use WithPagination;

    /** @var array<string, PluginBase> */
    protected array $plugins = [];

    public function template(): ?Theme
    {
        return null;
    }

    public function resolvePlugins(): void
    {
        if (! empty($this->plugins)) {
            return;
        }

        $plugins = PowerGridManager::$plugins;

        foreach ($plugins as $plugin) {
            $pluginInstance = new $plugin($this);
            if ($pluginInstance->isEnabled()) {
                $this->plugins[$pluginInstance->name()] = $pluginInstance;
            }
        }
    }

    /** @return array<string, PluginBase> */
    public function getPlugins(): array
    {
        return $this->plugins;
    }

    /** @param  array<int, mixed>  $arguments */
    public function handlePlugin(string $plugin, string $action, array $arguments = []): void
    {
        $this->resolvePlugins();
        if (isset($this->plugins[$plugin]) && method_exists($this->plugins[$plugin], $action)) {
            $this->plugins[$plugin]->{$action}(...$arguments);
        }
    }

    /**
     * @param  string  $method
     * @param  array<int, mixed>  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        $this->resolvePlugins();

        foreach ($this->plugins as $plugin) {
            if (method_exists($plugin, $method)) {
                return $plugin->$method(...$parameters);
            }
        }

        return parent::__call($method, $parameters);
    }

    /** @param  Column|array<string, mixed>|\stdClass  $column */
    public function renderColumnContent(Column|array|\stdClass $column, mixed $row): ?string
    {
        foreach ($this->plugins as $plugin) {
            if ($plugin->handles($column)) {
                return $plugin->render($column, $row);
            }
        }

        return null;
    }

    /**
     * Render the content every enabled plugin contributes to a UI zone
     * (e.g. 'header'). Unlike columns, zones aggregate every plugin's output.
     */
    public function renderPluginZone(string $zone): string
    {
        $this->resolvePlugins();

        $html = '';

        foreach ($this->plugins as $plugin) {
            if ($plugin->handlesZone($zone)) {
                $html .= $plugin->renderZone($zone) ?? '';
            }
        }

        return $html;
    }

    public function boot(): void
    {
        /** @var string $themeClass */
        $themeClass = $this->customThemeClass() ?? config('livewire-powergrid.theme');

        /** @var Theme $themeInstance */
        $themeInstance = app($themeClass);

        $customTheme = $this->template();

        if ($customTheme instanceof Theme) {
            $themeInstance = $customTheme;
        }

        ThemeManager::clearCache();
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
            $name = is_object($setUp) ? data_get($setUp, 'name') : null;
            if (is_string($name)) {
                $this->setUp[$name] = $setUp;
            }
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

    public function updatedPaginators(): void
    {
        $this->checkboxAll = false;

        partials($this)
            ->partial("pg-tbody-{$this->tableName}", 'livewire-powergrid::components.partials.tbody')
            ->partial("pg-pagination-{$this->tableName}", theme_view('footer'));
    }

    public function updated(string $name): void
    {
        if (str_contains($name, 'setUp.footer.perPage')) {
            partials($this)
                ->partial("pg-tbody-{$this->tableName}", 'livewire-powergrid::components.partials.tbody')
                ->partial("pg-pagination-{$this->tableName}", theme_view('footer'));
        }
    }

    public function updatedSearch(): void
    {
        $this->gotoPage(1, data_get($this->setUp, 'footer.pageName'));

        partials($this)
            ->partial("pg-tbody-{$this->tableName}", 'livewire-powergrid::components.partials.tbody')
            ->partial("pg-pagination-{$this->tableName}", theme_view('footer'));
    }

    #[Computed]
    public function hasColumnFilters(): bool
    {
        return collect($this->columns)
            ->filter(fn ($column) => filled(data_get($column, 'filters')))->count() > 0;
    }

    /** @return BaseCollection<int, Column> */
    #[Computed]
    public function visibleColumns(): BaseCollection
    {
        /** @var BaseCollection<int, Column> $columns */
        $columns = collect($this->columns)
            ->where('forceHidden', false)
            ->map(function ($column) {
                /** @var Column $column */
                data_forget($column, 'rawQueries');

                return $column;
            });

        return $columns;
    }

    #[Computed]
    protected function records(): mixed
    {
        if (! $this->readyToLoad) {
            return collect();
        }

        if (filled(data_get($this->setUp, 'cache.enabled'))) {
            return $this->getRecordsFromCache();
        }

        return $this->getRecordsDataSource();
    }

    private function getRecordsFromCache(): mixed
    {
        /** @var string $prefix */
        $prefix = data_get($this->setUp, 'cache.prefix');
        /** @var string $customTag */
        $customTag = data_get($this->setUp, 'cache.tag');
        /** @var int $ttl */
        $ttl = data_get($this->setUp, 'cache.ttl');

        if (filled($customTag)) {
            $tag = $prefix.$customTag;
        } else {
            /** @var object|string $datasource */
            $datasource = $this->datasource();
            $table = method_exists($datasource, 'getModel') ? $datasource->getModel()->getTable() : $this->tableName;
            $tag = $prefix.'powergrid-'.$table.'-'.$this->tableName;
        }
        $cacheKey = implode('-', $this->getCacheKeys());

        $getCacheClosure = fn () => ProcessDataSource::make($this)->get();

        /** @var array{results: mixed, transformTime: float} $results */
        $results = Cache::supportsTags()
            ? Cache::tags($tag)->remember($cacheKey, $ttl, $getCacheClosure)
            : Cache::remember($tag.'-'.$cacheKey, $ttl, $getCacheClosure);

        return $this->applyAfterQuery($results['results']);
    }

    /** @return AbstractPaginator<int, mixed>|MorphToMany<Model, Model>|BaseCollection<int, mixed> */
    private function getRecordsDataSource(): AbstractPaginator|MorphToMany|BaseCollection
    {
        $processResult = ProcessDataSource::make($this)->get();

        if ($processResult['results'] instanceof AbstractPaginator) {
            /** @var BaseCollection<int, mixed> $actionsRows */
            $actionsRows = $processResult['results']->getCollection();
        } else {
            /** @var array<int, mixed> $processResultsData */
            $processResultsData = $processResult['results'];
            /** @var BaseCollection<int, mixed> $actionsRows */
            $actionsRows = new BaseCollection($processResultsData);
        }

        return $this->applyAfterQuery($processResult['results']);
    }

    /** @return AbstractPaginator<int, mixed>|MorphToMany<Model, Model>|BaseCollection<int, mixed> */
    private function applyAfterQuery(mixed $results): AbstractPaginator|MorphToMany|BaseCollection
    {
        if ($results instanceof AbstractPaginator) {
            $results->setCollection($this->transformRows($results->getCollection()));

            return $results;
        }

        if ($results instanceof BaseCollection) {
            return $this->transformRows($results);
        }

        /** @var MorphToMany<Model, Model>|Collection<int, mixed> $results */
        return $results;
    }

    /** @return list<string|false> */
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
        /** @var view-string $viewName */
        $viewName = theme_view('table.no-data-label');

        return view($viewName);
    }

    /** @return array<string, mixed> */
    public function getPublicPropertiesDefinedInComponent(): array
    {
        return collect((new \ReflectionClass($this))->getProperties(\ReflectionProperty::IS_PUBLIC))
            ->where('class', get_class($this))
            ->pluck('name')
            ->intersect(array_keys($this->all()))
            ->mapWithKeys(function ($property): array {
                /** @var string $property */
                return [$property => $this->{$property}];
            })
            ->all();
    }

    public function toggleDetail(string $rowId): void
    {
        $this->dispatch('pg-toggle-detail-'.$this->tableName.'-'.$rowId, collapsed: null);
    }

    #[Computed]
    public function total(): ?int
    {
        /** @var object|string $records */
        $records = $this->records;

        if (method_exists($records, 'total')) {
            return $this->records->total();
        }

        if (method_exists($records, 'count')) {
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
        $this->resolvePlugins();

        /** @var view-string $viewName */
        $viewName = theme_view('table');

        return view($viewName);
    }
}
