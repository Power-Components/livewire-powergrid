<?php

namespace PowerComponents\LivewirePowerGrid\DataSource\Processors\Pipelines;

use Closure;
use Illuminate\Support\Str;
use PowerComponents\LivewirePowerGrid\{Column, PowerGridComponent};

class Summaries
{
    private const SUMMARIES = ['sum', 'count', 'avg', 'min', 'max'];

    public function __construct(protected PowerGridComponent $component) {}

    public function handle(mixed $query, Closure $next): mixed
    {
        if (! $this->component->hasSummarizeInColumns()) {
            return $next($query);
        }

        $this->component->columns = collect($this->component->columns)
            ->map(function ($column) use ($query) {
                $column = (object) $column;
                $field = strval(data_get($column, 'dataField')) ?: strval(data_get($column, 'field'));

                foreach (self::SUMMARIES as $summary) {
                    if (data_get($column, 'properties.summarize.'.$summary)) {
                        $value = $query->{$summary}($field);
                        $this->formatAndSetSummaryValue($column, $summary, $value);
                    }
                }

                return $column;
            })
            ->toArray();

        return $next($query);
    }

    private function formatAndSetSummaryValue(Column|\stdClass $column, string $summarizeMethod, mixed $value): void
    {
        $summarizeFormatTasks = $this->component->summarizeFormat();

        if (count($summarizeFormatTasks) > 0) {
            foreach ($summarizeFormatTasks as $field => $formattingClosure) {
                [$fieldName, $methods] = explode('.', $field);

                if (in_array($fieldName, [$column->field, $column->dataField])) {
                    $applyToMethods = Str::of($methods)
                        ->replaceMatches('/\s+/', '')
                        ->replace(['{', '}'], '')
                        ->explode(',')
                        ->all();

                    if (in_array($summarizeMethod, $applyToMethods) && is_callable($formattingClosure)) {
                        $value = $formattingClosure($value);
                    }
                }
            }
        }

        data_set($column, 'properties.summarize_values.'.$summarizeMethod, $value);
    }
}
