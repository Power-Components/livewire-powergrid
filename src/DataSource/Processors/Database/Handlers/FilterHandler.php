<?php

namespace PowerComponents\LivewirePowerGrid\DataSource\Processors\Database\Handlers;

use Illuminate\Database\Eloquent\{Builder as EloquentBuilder, Model};
use Illuminate\Database\Query\Builder as QueryBuilder;
use PowerComponents\LivewirePowerGrid\DataSource\Builders\{Boolean, DatePicker, DateTimePicker, InputText, MultiSelect, Number, Select};
use PowerComponents\LivewirePowerGrid\DataSource\Support\{FilterNormalizer, InputOperators};
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

class FilterHandler
{
    use InputOperators;

    public function __construct(
        private readonly PowerGridComponent $component
    ) {}

    /** @param  EloquentBuilder<Model>|QueryBuilder  $query
     * @return EloquentBuilder<Model>|QueryBuilder */
    public function apply(EloquentBuilder|QueryBuilder $query): EloquentBuilder|QueryBuilder
    {
        $filterDefinitions = collect($this->component->filters());

        if ($filterDefinitions->isEmpty() || empty($this->component->filters)) {
            return $query;
        }

        foreach ($this->component->filters as $filterType => $columns) {
            foreach (FilterNormalizer::normalize($columns) as $field => $value) {
                // Only apply a filter for a field that was actually declared in
                // filters(). Without this guard the field/column identifier is
                // user-controlled (the $filters property is mass-assignable), so
                // a crafted request could filter on an undeclared column.
                $hasDefinition = $filterDefinitions->contains(
                    fn ($filter) => data_get($filter, 'field') === $field
                );

                if (! $hasDefinition) {
                    continue;
                }

                $query->where(function ($query) use ($filterType, $field, $value, $filterDefinitions) {
                    $filter = function ($query, $filterDefinitions, $filterType, $field, $value) {
                        $filter = $filterDefinitions->filter(function ($filter) use ($field) {
                            return data_get($filter, 'field') === $field;
                        })
                            ->first();

                        match ($filterType) {
                            'datetime' => (new DateTimePicker($this->component, $filter))->builder($query, $field, $value),
                            'date' => (new DatePicker($this->component, $filter))->builder($query, $field, $value),
                            'multi_select' => (new MultiSelect($this->component, $filter))->builder($query, $field, $value),
                            'select' => (new Select($this->component, $filter))->builder($query, $field, $value),
                            'boolean' => (new Boolean($this->component, $filter))->builder($query, $field, $value),
                            'number' => (new Number($this->component, $filter))->builder($query, $field, $value),
                            'input_text' => (new InputText($this->component, $filter))->builder($query, $field, [
                                'selected' => $this->validateInputTextOptions($this->component->filters, $field),
                                'value' => $value,
                                'searchMorphs' => $this->component->searchMorphs(),
                            ]),
                            default => null
                        };
                    };

                    $filter($query, $filterDefinitions, $filterType, $field, $value);
                });
            }
        }

        return $query;
    }
}
