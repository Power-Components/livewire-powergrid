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

class ProcessDataSourceToRender
{
    use SoftDeletes;

    public bool $isCollection = false;

    public function __construct(
        public PowerGridComponent $component
    ) {
    }

    public static function fillData(PowerGridComponent $powerGridComponent): ProcessDataSourceToRender
    {
        return new self($powerGridComponent);
    }

    /**
     * @throws \Throwable
     */
    public function get(): BaseCollection|Paginator|\Illuminate\Pagination\LengthAwarePaginator|LengthAwarePaginator
    {
        $datasource = $this->prepareDataSource();

        if ($this->isCollection) {
            return $this->processCollection($datasource);
        }

        $this->getCurrentTable($datasource);

        return $this->processModel($datasource);
    }

    private function prepareDataSource(): QueryBuilder|BaseCollection|EloquentBuilder|MorphToMany
    {
        /** @var EloquentBuilder|QueryBuilder|BaseCollection|BaseCollection|MorphToMany $datasource */
        $datasource = (!empty($this->component->datasource)) ? $this->component->datasource : $this->component->datasource();

        /** @phpstan-ignore-next-line */
        if (is_array($this->component->datasource())) {
            /** @phpstan-ignore-next-line */
            $datasource = collect($this->component->datasource());
        }

        $this->isCollection = is_a((object) $datasource, BaseCollection::class);

        return $datasource;
    }

    private function processCollection(mixed $datasource): \Illuminate\Pagination\LengthAwarePaginator|BaseCollection
    {
        /** @var BaseCollection $datasource */
        cache()->forget($this->component->id);

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
    private function processModel(array|BaseCollection|null|MorphToMany|EloquentBuilder|QueryBuilder $datasource): Paginator|LengthAwarePaginator
    {
        /** @var EloquentBuilder|QueryBuilder|MorphToMany $results */
        $results = $this->resolveModel($datasource)
            ->where(
                fn (EloquentBuilder|QueryBuilder $query) => Builder::make($query, $this->component)
                    ->filterContains()
                    ->filter()
            );

        if (!$datasource instanceof QueryBuilder) {
            /** @var EloquentBuilder|MorphToMany $softDeleteResults */
            $softDeleteResults = $results;

            $results = self::applySoftDeletes($softDeleteResults);
        }

        $sortField = $this->makeSortField();

        if ($this->component->multiSort) {
            foreach ($this->component->sortArray as $sortField => $direction) {
                $sortField = Str::of($sortField)->contains('.') || $this->component->ignoreTablePrefix ? $sortField : $this->component->currentTable . '.' . $sortField;

                if ($this->component->withSortStringNumber) {
                    $results = self::applyWithSortStringNumber($results, $sortField, $direction);
                }
                $results = $results->orderBy($sortField, $direction);
            }
        } else {
            $results = self::applyWithSortStringNumber($results, $sortField);
            $results = $results->orderBy($sortField, $this->component->sortDirection);
        }

        self::applyTotalColumn($results);

        $results = self::applyPerPage($results);

        self::resolveDetailRow($results);

        if (method_exists($results, 'total')) {
            $this->component->total = $results->total();
        }

        /** @phpstan-ignore-next-line */
        return $results->setCollection($this->transform($results->getCollection()));
    }

    private function makeSortField(): string
    {
        return Str::of($this->component->sortField)->contains('.') || $this->component->ignoreTablePrefix
            ? $this->component->sortField : $this->component->currentTable . '.' . $this->component->sortField;
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

    public function resolveModel(array|BaseCollection|null|MorphToMany|EloquentBuilder|QueryBuilder $datasource = null): mixed
    {
        if (blank($datasource)) {
            return $this->component->datasource();
        }

        return $datasource;
    }

    /**
     * @throws \Exception
     */
    protected function resolveCollection(array|BaseCollection|EloquentBuilder|QueryBuilder|null $datasource = null): BaseCollection
    {
        if (!boolval(config('livewire-powergrid.cached_data', false))) {
            return new BaseCollection($this->component->datasource());
        }

        return cache()->rememberForever($this->component->id, function () use ($datasource) {
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

    private function getCurrentTable(MorphToMany|EloquentBuilder|BaseCollection|QueryBuilder $datasource): void
    {
        if ($datasource instanceof QueryBuilder) {
            $this->component->currentTable = $datasource->from;

            return;
        }

        /** @phpstan-ignore-next-line  */
        $this->component->currentTable = $datasource->getModel()->getTable();
    }
}
