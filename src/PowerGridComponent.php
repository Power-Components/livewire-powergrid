<?php

namespace PowerComponents\LivewirePowerGrid;

use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\{Factory, View};
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Pagination\{LengthAwarePaginator, Paginator};
use Illuminate\Support\{Collection as BaseCollection, Facades\Cache};
use Livewire\{Attributes\On, Component, WithPagination};
use PowerComponents\LivewirePowerGrid\Connectors\{Collection,};
use PowerComponents\LivewirePowerGrid\Themes\ThemeBase;
use PowerComponents\LivewirePowerGrid\Traits\{HasFilter,
    Listeners,
    PersistData,
    SoftDeletes,
    WithCheckbox,
    WithSorting};
use Throwable;

class PowerGridComponent extends Component
{
    use WithPagination;
    use WithSorting;
    use WithCheckbox;
    use HasFilter;
    use PersistData;
    use Listeners;
    use SoftDeletes;

    public array $headers = [];

    public string $search = '';

    public array $columns = [];

    public array $filtered = [];

    public string $primaryKey = 'id';

    public string $currentTable = '';

    public Collection|array|EloquentBuilder|QueryBuilder $datasource;

    public array $relationSearch = [];

    public bool $ignoreTablePrefix = true;

    public string $tableName = 'default';

    public bool $headerTotalColumn = false;

    public bool $footerTotalColumn = false;

    public array $setUp = [];

    public bool $showErrorBag = false;

    protected ThemeBase $powerGridTheme;

    public bool $rowIndex = true;

    public int $total = 0;

    public int $totalCurrentPage = 0;

    public array $searchMorphs = [];

    public bool $deferLoading = false;

    public bool $readyToLoad = false;

    public string $loadingComponent = '';

    public bool $showFilters = false;

    protected ?ProcessDataSource $processDataSourceInstance = null;

    public function mount(): void
    {
        $this->readyToLoad = !$this->deferLoading;

        foreach ($this->setUp() as $setUp) {
            $this->setUp[$setUp->name] = $setUp;
        }

        if (array_key_exists('detail', $this->setUp) && array_key_exists('responsive', $this->setUp)) {
            throw new Exception('The Feature Responsive cannot be used with Detail');
        }

        $this->columns = $this->columns();

        $this->resolveTotalRow();

        $this->restoreState();
    }

    private function getCacheKeys(): array
    {
        return [
            json_encode(['page' => $this->getPage()]),
            json_encode(['search' => $this->search]),
            json_encode(['sortDirection' => $this->sortDirection]),
            json_encode(['sortField' => $this->sortField]),
            json_encode(['filters' => $this->filters]),
        ];
    }

    public function showCheckBox(string $attribute = 'id'): PowerGridComponent
    {
        $this->checkbox          = true;
        $this->checkboxAttribute = $attribute;

        return $this;
    }

    private function resolveTotalRow(): void
    {
        collect($this->columns)->each(function (Column $column) {
            $hasHeader = $column->sum['header'] || $column->count['header'] || $column->min['header'] || $column->avg['header'] || $column->max['header'];
            $hasFooter = $column->sum['footer'] || $column->count['footer'] || $column->min['footer'] || $column->avg['footer'] || $column->max['footer'];

            if ($hasHeader) {
                $this->headerTotalColumn = true;
            }

            if ($hasFooter) {
                $this->footerTotalColumn = true;
            }
        });
    }

    public function fetchDatasource(): void
    {
        $this->readyToLoad = true;
    }

    private function getCachedData(): mixed
    {
        if (!Cache::supportsTags() || !boolval(data_get($this->setUp, 'cache.enabled'))) {
            return $this->readyToLoad ? $this->fillData() : collect([]);
        }

        $prefix    = strval(data_get($this->setUp, 'cache.prefix'));
        $customTag = strval(data_get($this->setUp, 'cache.tag'));
        $forever   = boolval(data_get($this->setUp, 'cache.forever', false));
        $ttl       = boolval(data_get($this->setUp, 'cache.ttl', false));

        $tag      = $prefix . ($customTag ?: 'powergrid-' . $this->datasource()->getModel()->getTable() . '-' . $this->tableName);
        $cacheKey = join('-', $this->getCacheKeys());

        if ($forever) {
            return $this->readyToLoad ? Cache::tags($tag)->rememberForever($cacheKey, fn () => $this->fillData()) : collect([]);
        }

        /** @phpstan-ignore-next-line */
        return $this->readyToLoad ? Cache::tags($tag)->remember($cacheKey, $ttl, fn () => $this->fillData()) : collect([]);
    }

    public function template(): ?string
    {
        return null;
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function searchMorphs(): array
    {
        return [];
    }

    /**
     * Apply checkbox, perPage and search view and theme
     */
    public function setUp(): array
    {
        return [];
    }

    public function columns(): array
    {
        return [];
    }

    public function filters(): array
    {
        return [];
    }

    /**
     * @return null
     */
    public function datasource()
    {
        return null;
    }

    public function summarizeFormat(): array
    {
        return [];
    }

    public function addColumns(): PowerGridColumns
    {
        return PowerGrid::columns();
    }

    public function checkedValues(): array
    {
        return $this->checkboxValues;
    }

    public function updatedPage(): void
    {
        $this->checkboxAll = false;
    }

    public function updatedSearch(): void
    {
        $this->gotoPage(1);
    }

    public function toggleFilters(): void
    {
        $this->showFilters = !$this->showFilters;
    }

    #[On('pg:toggleColumn-{tableName}')]
    public function toggleColumn(string $field): void
    {
        $this->columns = collect($this->columns)
            ->map(function ($column) use ($field) {
                if (data_get($column, 'field') === $field) {
                    data_set($column, 'hidden', !data_get($column, 'hidden'));
                }

                return (object) $column;
            })
            ->toArray();

        $this->persistState('columns');
    }

    public function toggleDetail(string $id): void
    {
        $detailStates = (array) data_get($this->setUp, 'detail.state');

        if (boolval(data_get($this->setUp, 'detail.collapseOthers'))) {
            /** @var \Illuminate\Support\Enumerable<(int|string), (int|string)> $except */
            $except = $id;
            collect($detailStates)->except($except)
                ->filter(fn ($state) => $state)->keys()
                ->each(
                    fn ($key) => data_set($this->setUp, "detail.state.$key", false)
                );
        }

        data_set($this->setUp, "detail.state.$id", !boolval(data_get($this->setUp, "detail.state.$id")));
    }

    public function refresh(): void
    {
        if (($this->total > 0) && ($this->totalCurrentPage - 1) === 0) {
            $this->previousPage();

            return;
        }

        $this->dispatch('$refresh', []);
    }

    public function getHasColumnFiltersProperty(): bool
    {
        return collect($this->columns)
                ->filter(fn ($column) => filled($column->filters))->count() > 0;
    }

    /**
     * @throws Exception
     * @throws Throwable
     */
    public function fillData(): BaseCollection|LengthAwarePaginator|\Illuminate\Contracts\Pagination\LengthAwarePaginator|Paginator|MorphToMany
    {
        $this->processDataSourceInstance = ProcessDataSource::fillData($this);

        return $this->processDataSourceInstance->get();
    }

    protected function powerGridListeners(): array
    {
        return [
            'pg:editable-' . $this->tableName     => 'inputTextChanged',
            'pg:toggleable-' . $this->tableName   => 'toggleableChanged',
            'pg:eventRefresh-' . $this->tableName => 'refresh',
            'pg:softDeletes-' . $this->tableName  => 'softDeletes',
        ];
    }

    /**
     * @return array
     */
    protected function getListeners()
    {
        return $this->powerGridListeners();
    }

    private function renderView(mixed $data): Application|Factory|View
    {
        /** @phpstan-var view-string $view */
        $view = $this->powerGridTheme->layout->table;

        return view($view, [
            'data'  => $data,
            'theme' => $this->powerGridTheme,
            'table' => 'livewire-powergrid::components.table',
        ]);
    }

    /**
     * @throws Exception|Throwable
     */
    public function render(): Application|Factory|View
    {
        $this->columns = $this->columns();

        $this->resolveFilters();

        /** @var ThemeBase $themeBase */
        $themeBase = PowerGrid::theme($this->template() ?? powerGridTheme());

        $this->powerGridTheme = $themeBase->apply();

        $this->columns = collect($this->columns)->map(function ($column) {
            return (object) $column;
        })->toArray();

        $this->relationSearch = $this->relationSearch();
        $this->searchMorphs   = $this->searchMorphs();

        $data = $this->getCachedData();

        /** @phpstan-ignore-next-line */
        $this->totalCurrentPage = method_exists($data, 'items') ? count($data->items()) : $data->count();

        return $this->renderView($data);
    }
}
