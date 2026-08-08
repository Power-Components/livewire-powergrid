<?php

namespace PowerComponents\LivewirePowerGrid;

use Exception;
use Illuminate\Contracts\View\{Factory, View};
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Foundation\Application;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\{Arr, Collection as BaseCollection, Facades\Cache};
use Illuminate\Support\Collection;
use Illuminate\View\ComponentAttributeBag;
use Livewire\{Attributes\Computed, Component, WithPagination};
use PowerComponents\LivewirePowerGrid\Components\Rules\RuleManager;
use PowerComponents\LivewirePowerGrid\DataSource\ProcessDataSource;
use PowerComponents\LivewirePowerGrid\Exceptions\TableNameCannotCalledDefault;
use PowerComponents\LivewirePowerGrid\Plugins\PluginBase;
use PowerComponents\LivewirePowerGrid\Support\{CellRenderer, ColumnViewModel, ThemeManager};
use PowerComponents\LivewirePowerGrid\Themes\Theme;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * @property-read mixed $records
 * @property-read bool $hasColumnFilters
 * @property-read array<int, Column>|BaseCollection<int, Column> $visibleColumns
 * @property-read array<int, ColumnViewModel> $columnViewModels
 * @property-read string $realPrimaryKey
 *
 * @method mixed datasource(mixed ...$args)
 * @method mixed actions(mixed $row)
 * @method mixed actionsFromView(object $row)
 */
class PowerGridComponent extends Component
{
    use Concerns\Base;
    use Concerns\Checkbox;
    use Concerns\Filter;
    use Concerns\FilterBuilder;
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
        $field = data_get($column, 'dataField', data_get($column, 'field'));

        if (! is_string($field) || $field === '') {
            return null;
        }

        foreach ($this->plugins as $plugin) {
            // The rendered column comes from the client snapshot, so its
            // pluginData is attacker-controllable. Only render plugin content
            // when the server-declared column for the field is handled too.
            if ($plugin->handles($column) && $plugin->isDeclaredField($field)) {
                return $plugin->render($column, $row);
            }
        }

        return null;
    }

    public function renderCells(object $row, int $rowIndex, ?int $childIndex, mixed $parentId, string|int $rowId): string
    {
        return (new CellRenderer($this))->render($row, $rowIndex, $childIndex, $parentId, $rowId);
    }

    /**
     * Build the <tr> attribute bag from the row's action rules, server-side.
     *
     * Replaces the previous client-side `pgRowAttributes` Alpine component, whose
     * rule attributes were read once at init() and never re-evaluated. On a
     * Livewire morph (e.g. flipping a Toggleable), Alpine kept the stale rule so a
     * highlighted row never "unpainted". Computing here lets the morph refresh the
     * class/attributes on every render.
     *
     * @param  ComponentAttributeBag  $attributes  the row's base bag (class, wire:key, ...)
     */
    public function rowAttributes(object $row, ComponentAttributeBag $attributes): ComponentAttributeBag
    {
        /** @var array<int, array<string, mixed>> $rules */
        $rules = (array) data_get($row, '__powergrid_rules', []);

        $ruleClasses = [];
        $extra = [];

        foreach ($rules as $rule) {
            if (! (data_get($rule, 'applyLoop') || data_get($rule, 'apply'))) {
                continue;
            }

            // Only Rule::rows() paints the <tr>; checkbox/radio/action rules are
            // consumed by their own elements (see the checkbox partial).
            if (data_get($rule, 'forAction') !== RuleManager::TYPE_ROWS) {
                continue;
            }

            $ruleAttributes = data_get($rule, 'attributes');

            if (! is_array($ruleAttributes)) {
                continue;
            }

            foreach ($ruleAttributes as $key => $value) {
                // Nested {key, value} form (e.g. arbitrary attribute pairs).
                if (is_array($value) && isset($value['key'], $value['value']) && is_scalar($value['key']) && is_scalar($value['value'])) {
                    $nestedKey = (string) $value['key'];
                    $extra[$nestedKey] ??= (string) $value['value'];

                    continue;
                }

                if (! is_scalar($value)) {
                    continue;
                }

                $value = (string) $value;

                if ($key === 'class') {
                    $ruleClasses[] = $value;

                    continue;
                }

                // First rule wins for a given attribute; later ones are appended.
                $extra[$key] = isset($extra[$key])
                    ? $extra[$key].' '.$value
                    : $value;
            }
        }

        // merge() appends to the existing class; base class stays intact.
        return $attributes->merge(
            array_merge($extra, ['class' => implode(' ', $ruleClasses)]),
            escape: false,
        );
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
        // The `columns` property is mass-assignable and hydrated from the
        // client snapshot, so it cannot be trusted as the render surface.
        // Only render columns whose field/dataField is actually declared in
        // the server-side columns() method; client state may only toggle the
        // `hidden` flag for already-declared columns.
        $declaredFields = collect($this->declaredColumns())
            ->map(fn ($column) => $this->columnString($column, 'dataField') ?: $this->columnString($column, 'field'))
            ->filter()
            ->flip();

        /** @var BaseCollection<int, Column> $columns */
        $columns = collect($this->columns)
            ->where('forceHidden', false)
            ->filter(function ($column) use ($declaredFields): bool {
                // Action/index columns carry no field but are legitimate
                // declared columns; they never render row data.
                if ((bool) data_get($column, 'isAction') || (bool) data_get($column, 'index')) {
                    return true;
                }

                $field = $this->columnString($column, 'dataField') ?: $this->columnString($column, 'field');

                return isset($declaredFields[$field]);
            })
            ->map(function ($column) {
                /** @var Column $column */
                data_forget($column, 'rawQueries');

                return $column;
            });

        return $columns;
    }

    /**
     * @return array<int, ColumnViewModel>
     */
    #[Computed]
    public function columnViewModels(): array
    {
        $tdClass = theme('table.layout.td');

        return $this->visibleColumns
            ->map(function ($column) use ($tdClass) {
                $field = $this->columnString($column, 'field');
                $dataField = $this->columnString($column, 'dataField');
                $contentClassField = $this->columnString($column, 'contentClassField');
                /** @var array<array-key, mixed>|string $contentClasses */
                $contentClasses = data_get($column, 'contentClasses', []);
                $bodyClass = $this->columnString($column, 'bodyClass');
                $bodyStyle = $this->columnString($column, 'bodyStyle');
                $hidden = (bool) data_get($column, 'hidden');

                $customView = data_get($column, 'customContent.view');
                $customParams = data_get($column, 'customContent.params', []);

                return new ColumnViewModel(
                    column: $column,
                    field: $field,
                    dataField: $dataField !== '' ? $dataField : $field,
                    isAction: (bool) data_get($column, 'isAction'),
                    index: (bool) data_get($column, 'index'),
                    contentClassField: $contentClassField,
                    contentClasses: $contentClasses,
                    hasCustomContent: is_string($customView) && $customView !== '',
                    customView: is_string($customView) ? $customView : null,
                    customParams: is_array($customParams) ? $customParams : [],
                    tdClass: Arr::toCssClasses([$tdClass, $bodyClass]),
                    tdStyle: Arr::toCssStyles(['display:none' => $hidden, $bodyStyle]),
                    spanClassStatic: is_array($contentClasses)
                        ? null
                        : Arr::toCssClasses([$contentClassField, $contentClasses]),
                );
            })
            ->values()
            ->all();
    }

    private function columnString(mixed $column, string $key): string
    {
        $value = data_get($column, $key, '');

        return is_scalar($value) ? (string) $value : '';
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
        /** @var int $ttl */
        $ttl = data_get($this->setUp, 'cache.ttl');

        $tag = $this->summariesCacheTag();
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
            json_encode(['filterBuilder' => $this->filterBuilder]),
            json_encode(['softDeletes' => $this->softDeletes]),
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
    public function processEmptyState(): string
    {
        $emptyState = $this->renderEmptyState();

        if ($emptyState instanceof View) {
            return $emptyState->with(
                [
                    'emptyState' => trans('livewire-powergrid::datatable.labels.no_data'),
                    'noDataLabel' => trans('livewire-powergrid::datatable.labels.no_data'), // @deprecated since 7.x, use $emptyState
                    'table' => 'livewire-powergrid::components.table',
                    'data' => [],
                ]
            )->render();
        }

        return "<span>{$emptyState}</span>";
    }

    /** @deprecated since 7.x, use processEmptyState() instead */
    #[Computed]
    public function processNoDataLabel(): string
    {
        return $this->processEmptyState();
    }

    public function renderEmptyState(): string|View
    {
        return $this->noDataLabel();
    }

    /** @deprecated since 7.x, use renderEmptyState() instead */
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

        if ($this->hasSummarizeInColumns()) {
            // Touch the dataset so the Summaries pipeline runs (when not served from
            // cache) before hydrating totals onto the columns for rendering.
            $this->records; // @phpstan-ignore-line

            $this->hydrateSummaries();
        }

        /** @var view-string $viewName */
        $viewName = theme_view('table');

        return view($viewName);
    }
}
