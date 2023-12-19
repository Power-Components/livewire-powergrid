<?php

namespace PowerComponents\LivewirePowerGrid;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\{Builder as EloquentBuilder, Model};
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\{Collection as BaseCollection, Facades\DB, Str};
use PowerComponents\LivewirePowerGrid\Components\Actions\ActionsController;
use PowerComponents\LivewirePowerGrid\Components\Rules\{RulesController};
use PowerComponents\LivewirePowerGrid\DataSource\{Builder, Collection};
use PowerComponents\LivewirePowerGrid\DataSource\{Support\Sql};
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
    public function prepareDataSource(): EloquentBuilder|BaseCollection|Collection|QueryBuilder|MorphToMany|null
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
        cache()->forget($this->component->getId());

        $filters = Collection::make($this->resolveCollection($datasource), $this->component)
            ->filterContains()
            ->filter();

        $results = $this->component->applySorting($filters);

        if ($results->count()) {
            $this->component->filtered = $results->pluck($this->component->primaryKey)->toArray();

            $paginated = Collection::paginate($results, intval(data_get($this->component->setUp, 'footer.perPage')));
            $results   = $paginated->setCollection($this->transform($paginated->getCollection()));
        };

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
            $results = $this->applySoftDeletes($results, $this->component->softDeletes);
        }

        $this->applySummaries($results);

        $sortField = $this->makeSortField($this->component->sortField);

        /** @phpstan-ignore-next-line */
        $results = $this->component->multiSort ? $this->applyMultipleSort($results) : $this->applySingleSort($results, $sortField);

        $results = $this->applyPerPage($results);

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

        $sortFieldType = Sql::getSortFieldType($sortField);

        if (Sql::isValidSortFieldType($sortFieldType)) {
            $results->orderByRaw(Sql::sortStringAsNumber($sortField) . ' ' . $direction);
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

        $count = $results->count();

        return $results->$paginate($count ?: 10);
    }

    /**
     * @throws \Exception
     */
    protected function resolveCollection(array|BaseCollection|EloquentBuilder|QueryBuilder|null $datasource = null): BaseCollection
    {
        if (!boolval(config('livewire-powergrid.cached_data', false))) {
            return new BaseCollection($this->component->datasource());
        }

        return cache()->rememberForever($this->component->getId(), function () use ($datasource) {
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
        $processedResults = collect();

        $results->chunk(3)
            ->each(function (BaseCollection $collection) use (&$processedResults) {
                $processedBatch   = $this->processBatch($collection);
                $processedResults = $processedResults->concat($processedBatch);
            });

        return $processedResults;
    }

    private function processBatch(BaseCollection $collection): BaseCollection
    {
        return $collection->map(function ($row) {
            $addColumns = $this->component->addColumns();
            $columns    = $addColumns->columns;
            $columns    = collect($columns);

            /** @phpstan-ignore-next-line */
            $data = $columns->mapWithKeys(fn ($column, $columnName) => (object) [$columnName => $column((object) $row)]);

            $prepareRules = collect();
            $actions      = collect();

            if (method_exists($this->component, 'actionRules')) {
                $prepareRules = resolve(RulesController::class)
                    ->execute($this->component->actionRules($row), (object)$row);
            }

            if (method_exists($this->component, 'actions')) {
                $actions = (new ActionsController($this->component, $prepareRules))
                    ->execute($this->component->actions($row), (object)$row);
            }

            $mergedData = $data->merge([
                'rules' => $prepareRules,
            ])->merge([
                'actions' => $actions,
            ]);

            return $row instanceof Model
                ? tap($row)->forceFill($mergedData->toArray())
                : (object) $mergedData->toArray();
        });
    }

    protected function setCurrentTable(EloquentBuilder|array|BaseCollection|MorphToMany|Collection|QueryBuilder|null $datasource): void
    {
        if ($datasource instanceof QueryBuilder) {
            $this->component->currentTable = $datasource->from;

            return;
        }

        /** @phpstan-ignore-next-line  */
        $this->component->currentTable = $datasource->getModel()->getTable();
    }

    private function applySummaries(MorphToMany|EloquentBuilder|BaseCollection|QueryBuilder $results): void
    {
        $format = function ($summarize, $column, $field, $value) {
            if (method_exists($this->component, 'summarizeFormat')) {
                $summarizeFormat = $this->component->summarizeFormat();

                if (count($summarizeFormat) === 0) {
                    data_set($column, 'summarize.' . $summarize, $value);

                    return;
                }

                foreach ($summarizeFormat as $field => $format) {
                    $parts = explode('.', $field);

                    if (isset($parts[1])) {
                        $formats                 = str($parts[1])->replace(['{', '}'], '');
                        $allowedSummarizeFormats = explode(',', $formats);

                        if (in_array($summarize, $allowedSummarizeFormats)) {
                            data_set($column, 'summarize.' . $summarize, $this->component->summarizeFormat()[$field]($value));
                        }
                    }
                }
            }
        };

        $this->component->columns = collect($this->component->columns)
            ->map(function (array|\stdClass|Column $column) use ($results, $format) {
                $field = strval(data_get($column, 'dataField')) ?: strval(data_get($column, 'field'));

                $summaries = ['sum', 'count', 'avg', 'min', 'max'];

                foreach ($summaries as $summary) {
                    if (data_get($column, $summary . '.header') || data_get($column, $summary . '.footer')) {
                        $value = $results->{$summary}($field);
                        $format($summary, $column, $field, $value);
                    }
                }

                return (object) $column;
            })->toArray();
    }
}
