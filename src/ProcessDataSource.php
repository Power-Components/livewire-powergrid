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
use Throwable;

class ProcessDataSource
{
    use Concerns\SoftDeletes;

    public bool $isCollection = false;

    private array $queryLog = [];

    public function __construct(
        public PowerGridComponent $component,
        public array $properties = [],
    ) {
    }

    public static function fillData(PowerGridComponent $powerGridComponent, array $properties = []): ProcessDataSource
    {
        return new self($powerGridComponent, $properties);
    }

    public function queryLog(): array
    {
        return $this->queryLog;
    }

    /**
     * @throws Throwable
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
     * @return EloquentBuilder|BaseCollection|Collection|QueryBuilder|MorphToMany|null
     */
    public function prepareDataSource(): EloquentBuilder|BaseCollection|Collection|QueryBuilder|MorphToMany|null
    {
        $datasource = $this->component->datasource ?? null;

        if (empty($datasource)) {
            $datasource = $this->component->datasource($this->properties);
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
            $results   = $paginated->setCollection($this->transform($paginated->getCollection(), $this->component));
        };

        return $results;
    }

    /**
     * @throws Throwable
     */
    private function processModel(EloquentBuilder|MorphToMany|QueryBuilder|BaseCollection|null $datasource): Paginator|LengthAwarePaginator
    {
        DB::enableQueryLog();

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
            $results = $this->applySoftDeletes($results, $this->component->softDeletes);
        }

        $this->applySummaries($results);

        $sortField = $this->makeSortField($this->component->sortField);

        $results = $this->component->multiSort ? $this->applyMultipleSort($results) : $this->applySingleSort($results, $sortField);

        $results = $this->applyPerPage($results);

        $this->setTotalCount($results);

        if (filled(data_get($this->component, 'setUp.lazy'))) {
            return $results;
        }

        /** @phpstan-ignore-next-line */
        $collection = $results->getCollection();

        /** @phpstan-ignore-next-line */
        $results = $results->setCollection($this->transform($collection, $this->component));

        $this->queryLog = DB::getQueryLog();

        DB::disableQueryLog();

        return $results;
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
        $pageName    = strval(data_get($this->component->setUp, 'footer.pageName', 'page'));
        $perPage     = intval(data_get($this->component->setUp, 'footer.perPage'));
        $recordCount = strval(data_get($this->component->setUp, 'footer.recordCount'));

        $paginate = match ($recordCount) {
            'min'   => 'simplePaginate',
            default => 'paginate',
        };

        if ($perPage > 0) {
            return $results->$paginate($perPage, pageName: $pageName);
        }

        $count = $results->count();

        return $results->$paginate($count ?: 10, pageName: $pageName);
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

    public static function transform(BaseCollection $results, PowerGridComponent $component): BaseCollection
    {
        $processedResults = collect();

        $results->chunk(3)
            ->each(function (BaseCollection $collection) use (&$processedResults, $component) {
                $processedBatch   = self::processBatch($collection, $component);
                $processedResults = $processedResults->concat($processedBatch);
            });

        return $processedResults;
    }

    private static function processBatch(BaseCollection $collection, PowerGridComponent $component): BaseCollection
    {
        if (method_exists($component, 'addColumns')) {
            /** @deprecated 6.x */
            /** @var array $columns */
            $columns = $component->addColumns()->columns;
            $fields  = collect($columns);
        } else {
            /** @var array $fields */
            $fields = $component->fields()->fields;
            $fields = collect($fields);
        }

        return $collection->map(function ($row, $index) use ($component, $fields) {
            /** @phpstan-ignore-next-line */
            $data = $fields->mapWithKeys(fn ($field, $fieldName) => (object) [$fieldName => $field((object) $row, $index)]);

            $prepareRules = collect();
            $actions      = collect();

            if (method_exists($component, 'actionRules')) {
                $prepareRules = resolve(RulesController::class)
                    ->execute($component->actionRules($row), (object)$row);
            }

            if (method_exists($component, 'actions')) {
                $actions = (new ActionsController($component, $prepareRules))
                    ->execute($component->actions($row), (object)$row);
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
        if (!$this->component->hasSummarizeInColumns()) {
            return;
        }

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
