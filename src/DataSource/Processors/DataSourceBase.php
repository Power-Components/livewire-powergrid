<?php

namespace PowerComponents\LivewirePowerGrid\DataSource\Processors;

use ArgumentCountError;
use Closure;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\{Builder as EloquentBuilder, Model};
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\{Collection as BaseCollection, Str};
use Illuminate\View\Concerns\ManagesLoops;
use InvalidArgumentException;
use Laravel\Scout\Builder as ScoutBuilder;
use PowerComponents\LivewirePowerGrid\{Column, Concerns\SoftDeletes, ManageLoops, PowerGridComponent};
use stdClass;

class DataSourceBase
{
    use ManagesLoops;
    use SoftDeletes;

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
     * @throws Exception
     */
    protected function applyMultipleSort(EloquentBuilder|QueryBuilder|MorphToMany $results): EloquentBuilder|QueryBuilder|MorphToMany
    {
        foreach ($this->component->sortArray as $sortField => $direction) {
            $results = $results->orderBy(
                $this->makeSortField($sortField),
                $direction
            );
        }

        return $results;
    }

    protected function makeSortField(string $sortField): string
    {
        if (Str::of($sortField)->contains('.') || $this->component->ignoreTablePrefix) {
            return $sortField;
        }

        return $this->component->currentTable . '.' . $sortField;
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

        $this->component->gotoPage(1, pageName: $pageName);

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

        $renderActions     = method_exists($component, 'actions');
        $renderActionRules = method_exists($component, 'actionRules');
        $actionsHtml       = &static::$actionsHtml;

        return $results->map(function ($row, $index) use ($component, $fields, $loopInstance, $renderActions, $renderActionRules, &$actionsHtml) {
            $row  = (object) $row;
            $data = $fields->map(fn ($field) => $field($row, $index));

            $rowId = data_get($row, $component->primaryKeyAlias ?? $component->primaryKey);

            if ($renderActions) {
                try {
                    $actions = collect($component->actions($row)) // @phpstan-ignore-line
                        ->map(function ($action) use ($row, $component, $renderActionRules) {
                            $can = data_get($action, 'can');

                            return [
                                'action'         => data_get($action, 'action'),
                                'can'            => $can instanceof Closure ? $can($row) : $can,
                                'slot'           => data_get($action, 'slot'),
                                'tag'            => data_get($action, 'tag'),
                                'icon'           => data_get($action, 'icon'),
                                'iconAttributes' => data_get($action, 'iconAttributes'),
                                'attributes'     => data_get($action, 'attributes'),
                                'rules'          => $renderActionRules ? $component->resolveActionRules($row) : [],
                            ];
                        });

                    $actionsHtml[$rowId] = $actions->all();
                } catch (ArgumentCountError $exception) {
                    static::throwArgumentCountError($exception); // @phpstan-ignore-line
                }
            }

            return tap($data->merge([
                '__powergrid_loop'  => $loopInstance->getLastLoop(),
                '__powergrid_rules' => $component->prepareActionRulesForRows($row, $loopInstance->getLastLoop()),
            ]), function () use ($loopInstance) {
                $loopInstance->incrementLoopIndices();
            })->pipe(function ($mergedData) use ($component, $row) {
                return $component->supportModel && $row instanceof Model // @phpstan-ignore-line
                    ? (object) tap($row)->forceFill($mergedData->all()) // @phpstan-ignore-line
                    : (object) $mergedData->all();
            });
        });
    }

    public function transformTime(): float
    {
        return self::$transformTime;
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
            $summarizeFormatTasks = $this->component->summarizeFormat();

            if (count($summarizeFormatTasks) === 0) {
                data_set($column, 'properties.summarize_values.' . $summarizeMethod, $value);

                return;
            }

            foreach ($summarizeFormatTasks as $field => $applySummaryFormat) {
                $fieldAndSummarizeMethods = explode('.', $field);

                if (count($fieldAndSummarizeMethods) != 2) {
                    throw new InvalidArgumentException('Summary Formatter expects key "column_name.{summarize_method}", [' . $field . '] given instead.');
                }

                $fieldName        = $fieldAndSummarizeMethods[0];
                $summarizeMethods = $fieldAndSummarizeMethods[1];

                $applyFormatToSummarizeMethods = str($summarizeMethods)->replaceMatches('/\s+/', '')
                    ->replace(['{', '}'], '')
                    ->explode(',')
                    ->all();

                if (in_array($summarizeMethod, $applyFormatToSummarizeMethods)) {
                    $formattingClosure = $this->component->summarizeFormat()[$field];

                    if (!is_callable($formattingClosure)) {
                        throw new InvalidArgumentException('Summary Formatter expects a callable function, ' . gettype($formattingClosure) . ' given instead.');
                    }

                    if (in_array($fieldName, [$column->field, $column->dataField])) {
                        $value = $formattingClosure($value);
                    }

                    data_set($column, 'properties.summarize_values.' . $summarizeMethod, $value);
                }
            }
        };

        $this->component->columns = collect($this->component->columns)
            ->map(function (array|stdClass|Column $column) use ($results, $applySummaryFormat) {
                $column = (object) $column;

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

    /**
     * @throws Exception
     */
    private static function throwArgumentCountError(Exception|ArgumentCountError $exception): void
    {
        $trace = $exception->getTrace();

        if (str(strval(data_get($trace, '0.file')))->contains('Macroable')) {
            $file = str(
                data_get($trace, '1.file') . ':' . data_get($trace, '1.line')
            )->after(base_path() . DIRECTORY_SEPARATOR);

            $method = strval(data_get($trace, '1.args.0'));

            throw new Exception("ArgumentCountError - method: [{$method}] - file: [{$file}]");
        }
    }
}
