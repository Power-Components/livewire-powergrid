<?php

namespace PowerComponents\LivewirePowerGrid\DataSource\Processors\Collection\Pipelines;

use Closure;
use Illuminate\Support\Collection;
use PowerComponents\LivewirePowerGrid\DataSource\Builders\{Boolean, DatePicker, DateTimePicker, InputText, MultiSelect, Number, Select};
use PowerComponents\LivewirePowerGrid\DataSource\Support\InputOperators;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class Filters
{
    use InputOperators;

    public function __construct(protected PowerGridComponent $component) {}

    public function handle(Collection $collection, Closure $next): Collection
    {
        if (blank($this->component->filters)) {
            return $next($collection);
        }

        $definitions = collect($this->component->filters());
        $results = $collection;

        foreach ($this->component->filters as $filterType => $columns) {
            foreach ($columns as $field => $value) {
                $definition = $definitions->first(fn ($filter) => data_get($filter, 'field') === $field);

                if (! $definition) {
                    continue;
                }

                $results = match ($filterType) {
                    'datetime' => (new DateTimePicker($this->component, $definition))->collection($results, $field, $value),
                    'date' => (new DatePicker($this->component, $definition))->collection($results, $field, $value),
                    'multi_select' => (new MultiSelect($this->component, $definition))->collection($results, $field, $value),
                    'select' => (new Select($this->component, $definition))->collection($results, $field, $value),
                    'boolean' => (new Boolean($this->component, $definition))->collection($results, $field, $value),
                    'number' => (new Number($this->component, $definition))->collection($results, $field, $value),
                    'input_text' => (new InputText($this->component, $definition))->collection($results, $field, [
                        'selected' => $this->validateInputTextOptions($this->component->filters, $field),
                        'value' => $value,
                    ]),
                    default => $results
                };
            }
        }

        return $next($results);
    }
}
