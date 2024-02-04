<?php

namespace PowerComponents\LivewirePowerGrid;

use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\{Factory, View};
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Pagination\{LengthAwarePaginator, Paginator};
use Illuminate\Support\{Arr, Collection as BaseCollection, Facades\Cache};

use Livewire\{Attributes\Computed, Component, Features\SupportQueryString\BaseUrl, WithPagination};

use function Livewire\{invade, store};

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

    protected function powerGridQueryString(): array
    {
        $queryString = [];

        foreach (Arr::dot($this->filters()) as $filter) {
            $as = str($filter->field)
                ->replace('_id', '');

            if ($as->contains('.')) {
                $as = $as->afterLast('.');
            }

            if ($filter->key === 'input_text') {
                $queryString['filters.input_text.' . $filter->field] = [
                    'as'     => $as->toString(),
                    'except' => '',
                ];

                $queryString['filters.input_text_options.' . $filter->field] = [
                    'as'     => $as->append('_operator')->toString(),
                    'except' => '',
                ];

                continue;
            }

            if ($filter->key === 'number') {
                $queryString['filters.number.' . $filter->field . '.start'] = [
                    'as'     => $as->append('_start')->toString(),
                    'except' => '',
                ];

                $queryString['filters.number.' . $filter->field . '.end'] = [
                    'as'     => $as->append('_end')->toString(),
                    'except' => '',
                ];

                continue;
            }

            $queryString['filters.' . $filter->key . '.' . $filter->field] = [
                'as'     => $as->toString(),
                'except' => '',
            ];
        }

        return $queryString;
    }

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

        $this->resolveFilters();
    }

    public function fetchDatasource(): void
    {
        $this->readyToLoad = true;
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields();
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
                ->filter(fn ($column) => filled($column->filters))->count() > 0;
    }

    #[Computed]
    public function visibleColumns(): BaseCollection
    {
        return collect($this->columns)
            ->where('forceHidden', false);
    }

    #[Computed]
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

    private function getTheme(): array
    {
        $class = $this->template() ?? powerGridTheme();

        return Cache::rememberForever('powerGridTheme_' . $class, function () use ($class) {
            /** @var ThemeBase $themeBase */
            $themeBase = PowerGrid::theme($class);

            return convertObjectsToArray((array) $themeBase->apply());
        });
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
        $theme = $this->getTheme();

        return view($theme['layout']['table'], [
            'data'  => $data,
            'theme' => $this->getTheme(),
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
