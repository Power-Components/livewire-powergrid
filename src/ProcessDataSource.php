<?php

namespace PowerComponents\LivewirePowerGrid;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\{Collection as BaseCollection, Str};
use PowerComponents\LivewirePowerGrid\Helpers\{ActionRender, ActionRules, Builder, Collection, SqlSupport};
use PowerComponents\LivewirePowerGrid\Traits\SoftDeletes;

/** @internal  */
class ProcessDataSource
{
    use SoftDeletes;

    public bool $isCollection = false;

    public function __construct(
        public PowerGridComponent $component
    ) {
    }

    public static function fillData(PowerGridComponent $powerGridComponent): ProcessDataSource
    {
        return new self($powerGridComponent);
    }

    /**
     * @throws \Throwable
     */
    public function get(): Paginator|LengthAwarePaginator|\Illuminate\Pagination\LengthAwarePaginator|BaseCollection
    {
        $datasource = $this->prepareDataSource();

        if ($datasource instanceof BaseCollection) {
            return $this->processCollection($datasource);
        }

        $this->setCurrentTable($datasource);

        /** @phpstan-ignore-next-line  */
        return $this->processModel($datasource);
    }

    /**
     * @return BaseCollection<(int|string), mixed>|Collection|EloquentBuilder|QueryBuilder|null
     */
    public function prepareDataSource(): EloquentBuilder|BaseCollection|Collection|QueryBuilder|null
    {
        $datasource = $this->component->datasource ?? null;

        if (empty($datasource)) {
            $datasource = $this->component->datasource();
        }

        if (is_array($datasource)) {
            $datasource = collect($datasource);
        }

        $this->isCollection = $datasource instanceof BaseCollection;

        return $datasource;
    }

    /**
     * @throws \Exception
     */
    private function processCollection(mixed $datasource): \Illuminate\Pagination\LengthAwarePaginator|BaseCollection
    {
        /** @var BaseCollection $datasource */
        cache()->forget($this->component->getLivewireId());

        $filters = Collection::make($this->resolveCollection($datasource), $this->component)
            ->filterContains()
            ->filter();

        $results = $this->component->applySorting($filters);

        if ($this->component->headerTotalColumn || $this->component->footerTotalColumn) {
            $this->component->withoutPaginatedData = $results->values()
                ->map(fn ($item) => (array) $item);
        }

        if ($results->count()) {
            $this->component->filtered = $results->pluck($this->component->primaryKey)->toArray();

            $paginated = Collection::paginate($results, intval(data_get($this->component->setUp, 'footer.perPage')));
            $results   = $paginated->setCollection($this->transform($paginated->getCollection()));
        }

        self::resolveDetailRow($results);

        return $results;
    }

    /**
     * @throws \Throwable
     */
    private function processModel(EloquentBuilder|MorphToMany|QueryBuilder|BaseCollection|null $datasource): Paginator|LengthAwarePaginator
    {
        if (is_null($datasource)) {
            $datasource = $this->component->datasource();
        }

        $results = $datasource
            ->where(
                fn (EloquentBuilder|QueryBuilder $query) => Builder::make($query, $this->component)
                    ->filterContains()
                    ->filter()
            );

        if ($datasource instanceof EloquentBuilder || $datasource instanceof MorphToMany) {
            /** @phpstan-ignore-next-line */
            $results = $this->applySoftDeletes($results);
        }

        $sortField = $this->makeSortField($this->component->sortField);

        /** @phpstan-ignore-next-line */
        $results = $this->component->multiSort ? $this->applyMultipleSort($results) : $this->applySingleSort($results, $sortField);

        $this->applyTotalColumn($results);

        $results = $this->applyPerPage($results);

        $this->resolveDetailRow($results);

        $this->setTotalCount($results);

        /** @phpstan-ignore-next-line */
        $collection = $results->getCollection();

        /** @phpstan-ignore-next-line */
        return $results->setCollection($this->transform($collection));
    }

    /**
     * @throws \Exception
     */
    private function applyMultipleSort(EloquentBuilder|QueryBuilder|MorphToMany $results): EloquentBuilder|QueryBuilder|MorphToMany
    {
        foreach ($this->component->sortArray as $sortField => $direction) {
            $sortField = $this->makeSortField($sortField);

            if ($this->component->withSortStringNumber) {
                $results = $this->applyWithSortStringNumber($results, $sortField, $direction);
            }
            $results = $results->orderBy($sortField, $direction);
        }

        return $results;
    }

    /**
     * @throws \Exception
     */
    private function applySingleSort(EloquentBuilder|QueryBuilder|MorphToMany|BaseCollection $results, string $sortField): MorphToMany|EloquentBuilder|QueryBuilder
    {
        /** @phpstan-ignore-next-line */
        $results = $this->applyWithSortStringNumber($results, $sortField);

        return $results->orderBy($sortField, $this->component->sortDirection);
    }

    private function setTotalCount(EloquentBuilder|MorphToMany|QueryBuilder|LengthAwarePaginator|Paginator $results): void
    {
        if (!method_exists($results, 'total')) {
            return;
        }

        $this->component->total = $results->total();
    }

    public function makeSortField(string $sortField): string
    {
        if (Str::of($sortField)->contains('.') || $this->component->ignoreTablePrefix) {
            return $sortField;
        }

        return $this->component->currentTable . '.' . $sortField;
    }

    private function applyTotalColumn(EloquentBuilder|QueryBuilder|MorphToMany $results): void
    {
        if ($this->component->headerTotalColumn || $this->component->footerTotalColumn) {
            $this->component->withoutPaginatedData = $this->transform($results->get());
        }
    }

    /**
     * @throws \Exception
     */
    private function applyWithSortStringNumber(
        EloquentBuilder|QueryBuilder|MorphToMany $results,
        string $sortField,
        string $multiSortDirection = null
    ): EloquentBuilder|QueryBuilder|MorphToMany {
        if (!$this->component->withSortStringNumber) {
            return $results;
        }

        $direction = $this->component->sortDirection;

        if ($multiSortDirection) {
            $direction = $multiSortDirection;
        }

        $sortFieldType = SqlSupport::getSortFieldType($sortField);

        if (SqlSupport::isValidSortFieldType($sortFieldType)) {
            $results->orderByRaw(SqlSupport::sortStringAsNumber($sortField) . ' ' . $direction);
        }

        return $results;
    }

    private function applyPerPage(EloquentBuilder|QueryBuilder|MorphToMany $results): LengthAwarePaginator|Paginator
    {
        $perPage     = intval(data_get($this->component->setUp, 'footer.perPage'));
        $recordCount = strval(data_get($this->component->setUp, 'footer.recordCount'));

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
        if (!isset($this->component->setUp['detail'])) {
            return;
        }

        $collection = $results;

        if (!$results instanceof BaseCollection) {
            $collection = collect($results->items());
        }

        /** @phpstan-ignore-next-line */
        $collection->each(function ($model) {
            $id = strval($model->{$this->component->primaryKey});

            data_set($this->component->setUp, 'detail', (array) $this->component->setUp['detail']);

            $state = data_get($this->component->setUp, 'detail.state.' . $id, false);

            data_set($this->component->setUp, 'detail.state.' . $id, $state);
        });
    }

    /**
     * @throws \Exception
     */
    protected function resolveCollection(array|BaseCollection|EloquentBuilder|QueryBuilder|null $datasource = null): BaseCollection
    {
        if (!boolval(config('livewire-powergrid.cached_data', false))) {
            return new BaseCollection($this->component->datasource());
        }

        return cache()->rememberForever($this->component->getLivewireId(), function () use ($datasource) {
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

    public function transform(BaseCollection $results): BaseCollection
    {
        return $results->map(function ($row) {
            $addColumns = $this->component->addColumns();

            $columns = $addColumns->columns;

            $columns = collect($columns);

            /** @phpstan-ignore-next-line */
            $data = $columns->mapWithKeys(fn ($column, $columnName) => (object) [$columnName => $column((object) $row)]);

            if (method_exists($this->component, 'actions') && count($this->component->actions())) {
                $actions = resolve(ActionRender::class)->resolveActionRender($this->component->actions(), (object) $row);
            }

            if (count($this->component->actionRules())) {
                $rules = resolve(ActionRules::class)->resolveRules($this->component->actionRules(), (object) $row);
            }

            $mergedData = $data->merge($rules ?? [])->merge($actions ?? []);

            return $row instanceof \Illuminate\Database\Eloquent\Model
                ? tap($row)->forceFill($mergedData->toArray())
                : (object) $mergedData->toArray();
        });
    }

    protected function setCurrentTable(EloquentBuilder|array|BaseCollection|Collection|QueryBuilder|null $datasource): void
    {
        if ($datasource instanceof QueryBuilder) {
            $this->component->currentTable = $datasource->from;

            return;
        }

        /** @phpstan-ignore-next-line  */
        $this->component->currentTable = $datasource->getModel()->getTable();
    }
}
