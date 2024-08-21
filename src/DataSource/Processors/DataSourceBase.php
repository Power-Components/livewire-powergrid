<?php

namespace PowerComponents\LivewirePowerGrid\DataSource\Processors;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\{Builder as EloquentBuilder, Model};
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\{Collection as BaseCollection, Str};
use Illuminate\View\Concerns\ManagesLoops;
use Laravel\Scout\Builder as ScoutBuilder;
use PowerComponents\LivewirePowerGrid\DataSource\Support\Sql;
use PowerComponents\LivewirePowerGrid\{Button, Column, Concerns\SoftDeletes, ManageLoops, PowerGridComponent};

class DataSourceBase
{
    use SoftDeletes;
    use ManagesLoops;

    public static float $transformTime = 0;

    public static array $actionsHtml = [];

    public function __construct(
        public PowerGridComponent $component,
        public bool $isExport = false
    ) {
    }

    public function prepareDataSource(): mixed
    {
        return $this->component->datasource($this->component->properties ?? []);
    }

    /**
     * @throws \Exception
     */
    protected function applyMultipleSort(EloquentBuilder|QueryBuilder|MorphToMany $results): EloquentBuilder|QueryBuilder|MorphToMany
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
    protected function applySingleSort(EloquentBuilder|QueryBuilder|MorphToMany|BaseCollection $results, string $sortField): MorphToMany|EloquentBuilder|QueryBuilder
    {
        /** @phpstan-ignore-next-line */
        $results = $this->applyWithSortStringNumber($results, $sortField);

        return $results->orderBy($sortField, $this->component->sortDirection);
    }

    protected function makeSortField(string $sortField): string
    {
        if (Str::of($sortField)->contains('.') || $this->component->ignoreTablePrefix) {
            return $sortField;
        }

        return $this->component->currentTable . '.' . $sortField;
    }

    /**
     * @throws \Exception
     */
    protected function applyWithSortStringNumber(
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

    protected function applyPerPage(EloquentBuilder|QueryBuilder|MorphToMany|ScoutBuilder $results): LengthAwarePaginator|Paginator
    {
        $pageName    = strval(data_get($this->component->setUp, 'footer.pageName', 'page'));
        $perPage     = intval(data_get($this->component->setUp, 'footer.perPage'));
        $recordCount = strval(data_get($this->component->setUp, 'footer.recordCount'));

        if ($results instanceof ScoutBuilder) {
            $paginate = match (true) {
                $recordCount == 'min'                                    => 'simplePaginate',
                ($this->component->paginateRaw && $recordCount == 'min') => 'simplePaginateRaw', // @phpstan-ignore-line
                $this->component->paginateRaw                            => 'paginateRaw',
                default                                                  => 'paginateSafe',
            };
        } else {
            $paginate = match (true) {
                $recordCount === 'min' => 'simplePaginate',
                default                => 'paginate',
            };
        }

        if ($perPage > 0) {
            return $results->$paginate($perPage, pageName: $pageName);
        }

        $count = $results->count(); // @phpstan-ignore-line

        return $results->$paginate($count ?: 10, pageName: $pageName);
    }

    protected function setTotalCount(EloquentBuilder|MorphToMany|QueryBuilder|LengthAwarePaginator|Paginator $results): void
    {
        if (!method_exists($results, 'total')) {
            return;
        }

        $this->component->total = $results->total();
    }

    public static function transform(
        BaseCollection $results,
        PowerGridComponent $component,
        bool $fromLazyChild = false
    ): BaseCollection {
        if ($fromLazyChild && $component->paginateRaw) {
            return $results;
        }

        $start = microtime(true);

        $process = self::processRows($results, $component);

        self::$transformTime = round((microtime(true) - $start) * 1000);

        return $process;
    }

    private static function processRows(BaseCollection $results, PowerGridComponent $component): BaseCollection
    {
        $fields = collect($component->fields()->fields);

        if ($component->paginateRaw) {
            $results = collect((array) data_get($results, 'hits'))->pluck('document');
        }

        $loopInstance = app(ManageLoops::class);
        $loopInstance->addLoop($results);

        $renderActions = false;

        if (method_exists($component, 'actions')) {
            $renderActions = true;
        }

        return $results->map(function ($row, $index) use ($component, $fields, $loopInstance, $renderActions) {
            $data = $fields->map(fn ($field) => $field((object) $row, $index));

            $loopInstance->incrementLoopIndices();

            $rowId = data_get($row, $component->realPrimaryKey);

            $hasCookieActionsForRow = isset($_COOKIE['pg_cookie_' . $component->tableName . '_row_' . $rowId]);

            if ($renderActions && !$hasCookieActionsForRow) {
                $actions = collect($component->actions((object) $row)) // @phpstan-ignore-line
                    ->transform(function (Button|array $action) use ($row, $component) {
                        return [
                            'slot'           => data_get($action, 'slot'),
                            'tag'            => data_get($action, 'tag'),
                            'icon'           => data_get($action, 'icon'),
                            'iconAttributes' => data_get($action, 'iconAttributes'),
                            'attributes'     => data_get($action, 'attributes'),
                            'rules'          => $component->resolveActionRules($action, $row),
                        ];
                    });

                static::$actionsHtml[$rowId] = $actions->toArray();
            }

            $mergedData = $data->merge([
                '__powergrid_loop'  => $loop = $loopInstance->getLastLoop(),
                '__powergrid_rules' => $component->prepareActionRulesForRows($row, $loop),
            ]);

            if ($component->supportModel && $row instanceof Model) {
                return (object) tap($row)->forceFill($mergedData->toArray());
            }

            return (object) $mergedData->toArray();
        });
    }

    protected function setCurrentTable(mixed $datasource): void
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

    protected function applySummaries(MorphToMany|EloquentBuilder|BaseCollection|QueryBuilder $results): void
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

                    $fieldName        = $fieldAndSummarizeMethods[0];
                    $summarizeMethods = $fieldAndSummarizeMethods[1];

                    $applyFormatToSummarizeMethods = str($summarizeMethods)->replaceMatches('/\s+/', '')
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

                        data_set($column, 'properties.summarize_values.' . $summarizeMethod, $value);
                    }
                }
            }
        };

        $this->component->columns = collect($this->component->columns)
            ->map(function (array|\stdClass|Column $column) use ($results, $applySummaryFormat) {
                $field = strval(data_get($column, 'dataField')) ?: strval(data_get($column, 'field'));

                $summaries = ['sum', 'count', 'avg', 'min', 'max'];

                foreach ($summaries as $summary) {
                    if (data_get($column, 'properties.summarize.' . $summary . '.header') || data_get($column, 'properties.summarize.' . $summary . '.footer')) {
                        $value = $results->{$summary}($field);
                        rescue(fn () => $applySummaryFormat($summary, $column, $field, $value), report: false);
                    }
                }

                return (object) $column;
            })->toArray();
    }

    public function transformTime(): float
    {
        return self::$transformTime;
    }
}
