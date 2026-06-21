<?php

namespace PowerComponents\LivewirePowerGrid\DataSource\Processors\Collection\Pipelines;

use Closure;
use Illuminate\Support\Collection;
use PowerComponents\LivewirePowerGrid\DataSource\Builders\{Boolean, DatePicker, DateTimePicker, InputText, MultiSelect, Number, Select};
use PowerComponents\LivewirePowerGrid\DataSource\Support\{FilterNormalizer, InputOperators};
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class Filters
{
    use InputOperators;

    public function __construct(protected PowerGridComponent $component) {}

    /**
     * @param  Collection<int, mixed>  $collection
     * @return Collection<int, mixed>
     */
    public function handle(Collection $collection, Closure $next): Collection
    {
        if (blank($this->component->filters)) {
            return $next($collection);
        }

        $definitions = collect($this->component->filters());
        $results = $collection;

        foreach ($this->component->filters as $filterType => $columns) {
            foreach (FilterNormalizer::normalize($columns) as $field => $value) {
                $definition = $definitions->first(fn ($filter) => data_get($filter, 'field') === $field);

                if (! $definition) {
                    continue;
                }

                $results = match ($filterType) {
                    'datetime' => (function () use ($results, $field, $value, $definition) {
                        /** @var array{start: string, end: string}|int|string|null $value */
                        return (new DateTimePicker($this->component, $definition))->collection($results, $field, $value);
                    })(),
                    'date' => (function () use ($results, $field, $value, $definition) {
                        /** @var array{start: string, end: string}|int|string|null $value */
                        return (new DatePicker($this->component, $definition))->collection($results, $field, $value);
                    })(),
                    'multi_select' => (function () use ($results, $field, $value, $definition) {
                        /** @var int|list<string>|string|null $value */
                        return (new MultiSelect($this->component, $definition))->collection($results, $field, $value);
                    })(),
                    'select' => (function () use ($results, $field, $value, $definition) {
                        /** @var array<string, mixed>|int|string|null $value */
                        return (new Select($this->component, $definition))->collection($results, $field, $value);
                    })(),
                    'boolean' => (function () use ($results, $field, $value, $definition) {
                        /** @var array<string, mixed>|int|string|null $value */
                        return (new Boolean($this->component, $definition))->collection($results, $field, $value);
                    })(),
                    'number' => (function () use ($results, $field, $value, $definition) {
                        /** @var array{start?: float|int|string, end?: float|int|string}|int|string|null $value */
                        return (new Number($this->component, $definition))->collection($results, $field, $value);
                    })(),
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
