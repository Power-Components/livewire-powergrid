<?php

namespace PowerComponents\LivewirePowerGrid;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\{Builder as EloquentBuilder, Model};
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\{Collection as BaseCollection, Facades\DB, Str, Stringable};
use Laravel\Scout\Builder as ScoutBuilder;
use PowerComponents\LivewirePowerGrid\Components\Actions\ActionsController;
use PowerComponents\LivewirePowerGrid\Components\Rules\{RulesController};
use PowerComponents\LivewirePowerGrid\DataSource\{Builder, Collection};
use PowerComponents\LivewirePowerGrid\DataSource\{Support\Sql};
use Throwable;

class ProcessDataSource
{
    use Concerns\SoftDeletes;

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
    public function get(bool $isExport = false): Paginator|LengthAwarePaginator|\Illuminate\Pagination\LengthAwarePaginator|BaseCollection
    {
        $datasource = $this->prepareDataSource();

        if ($datasource instanceof BaseCollection) {
            return $this->processCollection($datasource, $isExport);
        }

        if ($datasource instanceof ScoutBuilder) {
            return $this->processScoutCollection($datasource);
        }

        $this->setCurrentTable($datasource);

        return $this->processModel($datasource); // @phpstan-ignore-line
    }

    public function processScoutCollection(ScoutBuilder $datasource): Paginator|LengthAwarePaginator
    {
        $datasource->query = Str::of($datasource->query)
            ->when($this->component->search != '', fn (Stringable $self) => $self
                ->prepend($this->component->search . ','))
            ->toString();

        collect($this->component->filters)->each(fn (array $filters) => collect($filters)
            ->each(fn (string $value, string $field) => $datasource
                ->where($field, $value)));

        if ($this->component->multiSort) {
            foreach ($this->component->sortArray as $sortField => $direction) {
                $datasource->orderBy($sortField, $direction);
            }
        } else {
            $datasource->orderBy($this->component->sortField, $this->component->sortDirection);
        }

        $results = self::applyPerPage($datasource);

        if (method_exists($results, 'total')) {
            $this->component->total = $results->total();
        }

        return $results->setCollection( // @phpstan-ignore-line
            $this->transform($results->getCollection(), $this->component) // @phpstan-ignore-line
        );
    }

    public function prepareDataSource(): EloquentBuilder|BaseCollection|Collection|QueryBuilder|MorphToMany|ScoutBuilder|null
    {
        $datasource = $this->component->datasource ?? null;

        if (empty($datasource)) {
            $datasource = $this->component->datasource($this->properties);
        }

        if (is_array($datasource)) {
            $datasource = collect($datasource);
        }

        return $datasource;
    }

    /**
     * @throws \Exception
     */
    private function processCollection(mixed $datasource, bool $isExport = false): \Illuminate\Pagination\LengthAwarePaginator|BaseCollection
    {
        /** @var BaseCollection $datasource */
        cache()->forget($this->component->getId());

        $filters = Collection::make($this->resolveCollection($datasource), $this->component)
            ->filterContains()
            ->filter();

        $results = $this->component->applySorting($filters);

        $this->applySummaries($results);

        $this->component->total = $results->count();

        if ($results->count()) {
            $this->component->filtered = $results->pluck($this->component->primaryKey)->toArray();

            $perPage   = $isExport ? $this->component->total : intval(data_get($this->component->setUp, 'footer.perPage'));
            $paginated = Collection::paginate($results, $perPage);
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

    private function applyPerPage(EloquentBuilder|QueryBuilder|MorphToMany|ScoutBuilder $results): LengthAwarePaginator|Paginator
    {
        $pageName    = strval(data_get($this->component->setUp, 'footer.pageName', 'page'));
        $perPage     = intval(data_get($this->component->setUp, 'footer.perPage'));
        $recordCount = strval(data_get($this->component->setUp, 'footer.recordCount'));

        $paginate = match ($recordCount) {
            'min'   => 'simplePaginate',
            default => 'paginate',
        };

        if ($results instanceof ScoutBuilder) {
            return $results->paginateSafe($perPage, pageName: $pageName); // @phpstan-ignore-line
        }

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
            /** @var string $from */
            $from                          = $datasource->from;
            $this->component->currentTable = $from;

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

        $applySummaryFormat = function ($summarizeMethod, $column, $field, $value) {
            if (method_exists($this->component, 'summarizeFormat')) {
                $summarizeFormatTasks = $this->component->summarizeFormat();

                if (count($summarizeFormatTasks) === 0) {
                    data_set($column, 'summarize.' . $summarizeMethod, $value);

                    return;
                }

                foreach ($summarizeFormatTasks as $field => $applySummaryFormat) {
                    $fieldAndSummarizeMethods = explode('.', $field);

                    if (count($fieldAndSummarizeMethods) != 2) {
                        throw new \InvalidArgumentException('Summary Formatter expects key "column_name.{summarize_method}", [' . $field . '] given instead.');
                    }

                    $fieldName       = $fieldAndSummarizeMethods[0];
                    $sumarizeMethods = $fieldAndSummarizeMethods[1];

                    $applyFormatToSummarizeMethods = str($sumarizeMethods)->replaceMatches('/\s+/', '')
                        ->replace(['{', '}'], '')
                        ->explode(',')
                        ->toArray();

                    if (in_array($summarizeMethod, $applyFormatToSummarizeMethods)) {
                        $formattingClosure = $this->component->summarizeFormat()[$field];

                        if (!is_callable($formattingClosure)) {
                            throw new \InvalidArgumentException('Summary Formatter expects a callable function, ' . gettype($formattingClosure) . ' given instead.');
                        }

                        if (in_array($fieldName, [$column->field, $column->dataField])) {
                            $value = $formattingClosure($value);
                        }

                        data_set($column, 'summarize.' . $summarizeMethod, $value);
                    }
                }
            }
        };

        $this->component->columns = collect($this->component->columns)
            ->map(function (array|\stdClass|Column $column) use ($results, $applySummaryFormat) {
                $field = strval(data_get($column, 'dataField')) ?: strval(data_get($column, 'field'));

                $summaries = ['sum', 'count', 'avg', 'min', 'max'];

                foreach ($summaries as $summary) {
                    if (data_get($column, $summary . '.header') || data_get($column, $summary . '.footer')) {
                        $value = $results->{$summary}($field);
                        rescue(fn () => $applySummaryFormat($summary, $column, $field, $value), report: false);
                    }
                }

                return (object) $column;
            })->toArray();
    }
}
