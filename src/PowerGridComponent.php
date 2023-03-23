<?php

namespace PowerComponents\LivewirePowerGrid;

use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\{Factory, View};
use Illuminate\Database\Eloquent as Eloquent;
use Illuminate\Database\Eloquent\Concerns\HasAttributes;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\{Builder, SoftDeletes};
use Illuminate\Pagination\Paginator;
use Illuminate\Support\{Collection as BaseCollection, Str};
use Livewire\{Component, WithPagination};
use PowerComponents\LivewirePowerGrid\Helpers\{ActionRules, Collection, Model, SqlSupport};
use PowerComponents\LivewirePowerGrid\Themes\ThemeBase;
use PowerComponents\LivewirePowerGrid\Traits\{HasFilter,
    Listeners,
    PersistData,
    WithCheckbox,
    WithSorting};
use Throwable;

class PowerGridComponent extends Component
{
    use WithPagination;
    use WithSorting;
    use WithCheckbox;
    use HasAttributes;
    use HasFilter;
    use PersistData;
    use Listeners;

    public array $headers = [];

    public string $search = '';

    public array $columns = [];

    public array $filtered = [];

    public string $primaryKey = 'id';

    public bool $isCollection = false;

    public string $currentTable = '';

    public Collection|array|Builder $datasource;

    public BaseCollection $withoutPaginatedData;

    public array $relationSearch = [];

    public bool $ignoreTablePrefix = true;

    public string $tableName = 'default';

    public bool $headerTotalColumn = false;

    public bool $footerTotalColumn = false;

    public array $setUp = [];

    public bool $showErrorBag = false;

    public string $softDeletes = '';

    protected ThemeBase $powerGridTheme;

    public bool $rowIndex = true;

    public int $total = 0;

    public int $totalCurrentPage = 0;

    public array $searchMorphs = [];

    public bool $deferLoading = false;

    public bool $readyToLoad;

    public function mount(): void
    {
        $this->readyToLoad = !$this->deferLoading;

        foreach ($this->setUp() as $setUp) {
            $this->setUp[$setUp->name] = $setUp;
        }

        if (isBootstrap5()) {
            unset($this->setUp['detail']);
        }

        $this->columns = $this->columns();

        $this->resolveTotalRow();

        $this->resolveFilters();

        $this->restoreState();
    }

    public function filters(): array
    {
        return [];
    }

    public function showCheckBox(string $attribute = 'id'): PowerGridComponent
    {
        $this->checkbox          = true;
        $this->checkboxAttribute = $attribute;

        return $this;
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

    /**
     * @throws Exception|Throwable
     */
    public function render(): Application|Factory|View
    {
        /** @var ThemeBase $themeBase */
        $themeBase = PowerGrid::theme($this->template() ?? powerGridTheme());

        $this->powerGridTheme = $themeBase->apply();

        $this->columns = collect($this->columns)->map(function ($column) {
            return (object) $column;
        })->toArray();

        $this->relationSearch = $this->relationSearch();
        $this->searchMorphs   = $this->searchMorphs();

        $data = $this->readyToLoad ? $this->fillData() : collect([]);

        if (method_exists($this, 'initActions')) {
            $this->initActions();

            if (method_exists($this, 'header')) {
                $this->headers = $this->header();
            }
        }

        /** @phpstan-ignore-next-line */
        $this->totalCurrentPage = method_exists($data, 'items') ? count($data->items()) : $data->count();

        return $this->renderView($data);
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

    public function updatedSearch(): void
    {
        $this->gotoPage(1);
    }

    /**
     * @throws Exception
     * @throws Throwable
     */
    public function fillData(): mixed
    {
        /** @var Builder|BaseCollection|BaseCollection|MorphToMany $datasource */
        $datasource = (!empty($this->datasource)) ? $this->datasource : $this->datasource();

        /** @phpstan-ignore-next-line */
        if (is_array($this->datasource())) {
            /** @phpstan-ignore-next-line */
            $datasource = collect($this->datasource());
        }

        $this->isCollection = is_a((object) $datasource, BaseCollection::class);

        if ($this->isCollection) {
            /** @var Builder|BaseCollection|BaseCollection $datasource */
            cache()->forget($this->id);

            $filters = Collection::make($this->resolveCollection($datasource), $this)
                ->filterContains()
                ->filter();

            $results = $this->applySorting($filters);

            if ($this->headerTotalColumn || $this->footerTotalColumn) {
                $this->withoutPaginatedData = $results->values()
                    ->map(fn ($item) => (array) $item);
            }

            if ($results->count()) {
                $this->filtered = $results->pluck($this->primaryKey)->toArray();

                $paginated = Collection::paginate($results, intval(data_get($this->setUp, 'footer.perPage')));
                $results   = $paginated->setCollection($this->transform($paginated->getCollection()));
            }

            self::resolveDetailRow($results);

            return $results;
        }

        /** @phpstan-ignore-next-line */
        $this->currentTable = $datasource->getModel()->getTable();

        $sortField = Str::of($this->sortField)->contains('.') || $this->ignoreTablePrefix
            ? $this->sortField : $this->currentTable . '.' . $this->sortField;

        /** @var Builder $results */
        $results = $this->resolveModel($datasource)
            ->where(
                fn (Builder $query) => Model::make($query, $this)
                ->filterContains()
                ->filter()
            );

        $results = self::applySoftDeletes($results);

        if ($this->multiSort) {
            foreach ($this->sortArray as $sortField => $direction) {
                $sortField = Str::of($sortField)->contains('.') || $this->ignoreTablePrefix ? $sortField : $this->currentTable . '.' . $sortField;

                if ($this->withSortStringNumber) {
                    $results = self::applyWithSortStringNumber($results, $sortField, $direction);
                }
                $results = $results->orderBy($sortField, $direction);
            }
        } else {
            $results = self::applyWithSortStringNumber($results, $sortField);
            $results = $results->orderBy($sortField, $this->sortDirection);
        }

        self::applyTotalColumn($results);

        $results = self::applyPerPage($results);

        self::resolveDetailRow($results);

        if (method_exists($results, 'total')) {
            $this->total = $results->total();
        }

        /** @phpstan-ignore-next-line */
        return $results->setCollection($this->transform($results->getCollection()));
    }

    private function applyTotalColumn(Builder|MorphToMany $results): void
    {
        if ($this->headerTotalColumn || $this->footerTotalColumn) {
            $this->withoutPaginatedData = $this->transform($results->get());
        }
    }

    /**
     * @throws Exception
     */
    private function applyWithSortStringNumber(
        Builder|MorphToMany $results,
        string $sortField,
        string $multiSortDirection = null
    ): Builder|MorphToMany {
        if (!$this->withSortStringNumber) {
            return $results;
        }

        $direction = $this->sortDirection;

        if ($multiSortDirection) {
            $direction = $multiSortDirection;
        }

        $sortFieldType = SqlSupport::getSortFieldType($sortField);

        if (SqlSupport::isValidSortFieldType($sortFieldType)) {
            $results->orderByRaw(SqlSupport::sortStringAsNumber($sortField) . ' ' . $direction);
        }

        return $results;
    }

    private function applyPerPage(Builder|MorphToMany $results): LengthAwarePaginator|Paginator
    {
        $perPage     = intval(data_get($this->setUp, 'footer.perPage'));
        $recordCount = strval(data_get($this->setUp, 'footer.recordCount'));

        $paginate = match ($recordCount) {
            'min'   => 'simplePaginate',
            default => 'paginate',
        };

        if ($perPage > 0) {
            return $results->$paginate($perPage);
        }

        return $results->$paginate($results->count());
    }

    private function resolveDetailRow(Paginator|LengthAwarePaginator|BaseCollection $results): void
    {
        if (!isset($this->setUp['detail'])) {
            return;
        }

        $collection = $results;

        if (!$results instanceof BaseCollection) {
            $collection = collect($results->items());
        }

        /** @phpstan-ignore-next-line */
        $collection->each(function ($model) {
            $id = strval($model->{$this->primaryKey});

            data_set($this->setUp, 'detail', (array) $this->setUp['detail']);

            $state = data_get($this->setUp, 'detail.state.' . $id, false);

            data_set($this->setUp, 'detail.state.' . $id, $state);
        });
    }

    /**
     * @return null
     */
    public function datasource()
    {
        return null;
    }

    /**
     * @throws Exception
     */
    protected function resolveCollection(array|BaseCollection|Builder|null $datasource = null): BaseCollection
    {
        if (!boolval(config('livewire-powergrid.cached_data', false))) {
            return new BaseCollection($this->datasource());
        }

        return cache()->rememberForever($this->id, function () use ($datasource) {
            if (is_array($datasource)) {
                return new BaseCollection($datasource);
            }

            if (is_a((object) $datasource, BaseCollection::class)) {
                return $datasource;
            }

            /** @var array $datasource */
            return new BaseCollection($datasource);
        });
    }

    protected function transform(BaseCollection $results): BaseCollection
    {
        if (!is_a((object) $this->addColumns(), PowerGridEloquent::class)) {
            return $results;
        }

        return $results->map(function ($row) {
            $addColumns = $this->addColumns();

            $columns = $addColumns->columns;

            $columns = collect($columns);

            /** @phpstan-ignore-next-line */
            $data = $columns->mapWithKeys(fn ($column, $columnName) => (object) [$columnName => $column((object) $row)]);

            if (count($this->actionRules())) {
                $rules = resolve(ActionRules::class)->resolveRules($this->actionRules(), (object) $row);
            }

            $mergedData = $data->merge($rules ?? []);

            return $row instanceof Eloquent\Model
                ? tap($row)->forceFill($mergedData->toArray())
                : (object) $mergedData->toArray();
        });
    }

    public function addColumns(): PowerGridEloquent
    {
        return PowerGrid::eloquent();
    }

    public function actionRules(): array
    {
        return [];
    }

    public function resolveModel(array|BaseCollection|Builder|null|MorphToMany $datasource = null): BaseCollection|array|null|Builder|MorphToMany
    {
        if (blank($datasource)) {
            return $this->datasource();
        }

        return $datasource;
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

    public function checkedValues(): array
    {
        return $this->checkboxValues;
    }

    public function updatedPage(): void
    {
        $this->checkboxAll = false;
    }

    /**
     * @throws Exception
     */
    public function toggleColumn(string $field): void
    {
        $this->columns = collect($this->columns)->map(function ($column) use ($field) {
            if (data_get($column, 'field') === $field) {
                data_set($column, 'hidden', !data_get($column, 'hidden'));
            }

            return (object) $column;
        })->toArray();

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

    public function softDeletes(string $softDeletes): void
    {
        $this->softDeletes = $softDeletes;
    }

    /**
     * @throws Throwable
     */
    private function applySoftDeletes(Builder|MorphToMany $results): Builder|MorphToMany
    {
        throw_if(
            $this->softDeletes && !in_array(SoftDeletes::class, class_uses(get_class($results->getModel())), true),
            new Exception(get_class($results->getModel()) . ' is not using the \Illuminate\Database\Eloquent\SoftDeletes trait')
        );

        return match ($this->softDeletes) {
            'withTrashed' => $results->withTrashed(), /** @phpstan-ignore-line */
            'onlyTrashed' => $results->onlyTrashed(), /** @phpstan-ignore-line */
            default       => $results
        };
    }

    public function refresh(): void
    {
        if (($this->total > 0) && ($this->totalCurrentPage - 1) === 0) {
            $this->previousPage();

            return;
        }

        $this->emitSelf('$refresh', []);
    }

    protected function powerGridListeners(): array
    {
        return [
            'pg:datePicker-' . $this->tableName   => 'datePickerChanged',
            'pg:editable-' . $this->tableName     => 'inputTextChanged',
            'pg:toggleable-' . $this->tableName   => 'toggleableChanged',
            'pg:multiSelect-' . $this->tableName  => 'multiSelectChanged',
            'pg:toggleColumn-' . $this->tableName => 'toggleColumn',
            'pg:eventRefresh-' . $this->tableName => 'refresh',
            'pg:softDeletes-' . $this->tableName  => 'softDeletes',
        ];
    }

    public function getHasColumnFiltersProperty(): bool
    {
        return collect($this->columns)
                ->filter(fn ($column) => filled($column->filters))->count() > 0;
    }

    /**
     * @return array
     */
    protected function getListeners()
    {
        return $this->powerGridListeners();
    }
}
