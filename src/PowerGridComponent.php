<?php

namespace PowerComponents\LivewirePowerGrid;

use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\{Factory, View};
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Pagination\{LengthAwarePaginator, Paginator};
use Illuminate\Support\{Collection as BaseCollection, Facades\Cache};

use function Livewire\store;

use Livewire\{Attributes\Computed, Component, WithPagination};
use PowerComponents\LivewirePowerGrid\Themes\ThemeBase;
use Throwable;

/**
 * @property-read mixed $getCachedData
 * @property-read bool $hasColumnFilters
 * @property-read array|BaseCollection $visibleColumns
 */
class PowerGridComponent extends Component
{
    use WithPagination;
    use Concerns\Base;
    use Concerns\Sorting;
    use Concerns\Checkbox;
    use Concerns\Filter;
    use Concerns\Persist;
    use Concerns\LazyManager;
    use Concerns\ToggleDetail;
    use Concerns\Hooks;
    use Concerns\Listeners;
    use Concerns\Summarize;
    use Concerns\SoftDeletes;

    public function mount(): void
    {
        $this->readyToLoad = !$this->deferLoading;

        foreach ($this->setUp() as $setUp) {
            $this->setUp[$setUp->name] = $setUp;
        }

        $this->throwColumnAction();

        $this->columns = $this->columns();

        $this->resolveTotalRow();

        $this->restoreState();
    }

    public function fetchDatasource(): void
    {
        $this->readyToLoad = true;
    }

    public function addColumns(): PowerGridColumns
    {
        return PowerGrid::columns();
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

    #[Computed(persist: true)]
    public function hasColumnFilters(): bool
    {
        return collect($this->columns)
                ->filter(fn ($column) => filled($column->filters))->count() > 0;
    }

    #[Computed(persist: true)]
    public function visibleColumns(): BaseCollection
    {
        return collect($this->columns)
            ->where('forceHidden', false);
    }

    #[Computed(persist: true)]
    protected function getCachedData(): mixed
    {
        if (!Cache::supportsTags() || !boolval(data_get($this->setUp, 'cache.enabled'))) {
            return $this->readyToLoad ? $this->fillData() : collect();
        }

        if (!$this->readyToLoad) {
            return collect();
        }

        $prefix    = strval(data_get($this->setUp, 'cache.prefix'));
        $customTag = strval(data_get($this->setUp, 'cache.tag'));
        $forever   = boolval(data_get($this->setUp, 'cache.forever', false));
        $ttl       = intval(data_get($this->setUp, 'cache.ttl'));

        $tag      = $prefix . ($customTag ?: 'powergrid-' . $this->datasource()->getModel()->getTable() . '-' . $this->tableName);
        $cacheKey = join('-', $this->getCacheKeys());

        return $forever
            ? Cache::tags($tag)->rememberForever($cacheKey, fn () => $this->fillData())
            : Cache::tags($tag)->remember($cacheKey, $ttl, fn () => $this->fillData());
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

    private function getTheme(string $key = null): array
    {
        $store = store($this)->get('powerGridTheme');

        if (!$key) {
            return convertObjectsToArray((array) $store);
        }

        return convertObjectsToArray((array) data_get($store, $key));
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

    private function renderView(mixed $data): Application|Factory|View
    {
        /** @phpstan-var view-string $view */
        $view = $this->getTheme('layout')['table'];

        return view($view, [
            'data'  => $data,
            'theme' => $this->getTheme(),
            'table' => 'livewire-powergrid::components.table',
        ]);
    }

    /**
     * @throws Exception|Throwable
     */
    public function render(): Application|Factory|View
    {
        $this->resolveFilters();

        /** @var ThemeBase $themeBase */
        $themeBase = PowerGrid::theme($this->template() ?? powerGridTheme());

        if (!store($this)->has('powerGridTheme')) {
            store($this)->set('powerGridTheme', $themeBase->apply());
        }

        $this->columns = collect($this->columns)->map(function ($column) {
            return (object) $column;
        })->toArray();

        $this->relationSearch = $this->relationSearch();
        $this->searchMorphs   = $this->searchMorphs();

        $data = $this->getCachedData();

        if (empty(data_get($this->setUp, 'lazy'))) {
            $this->resolveDetailRow($data);
        }

        /** @phpstan-ignore-next-line */
        $this->totalCurrentPage = method_exists($data, 'items') ? count($data->items()) : $data->count();

        return $this->renderView($data);
    }
}
