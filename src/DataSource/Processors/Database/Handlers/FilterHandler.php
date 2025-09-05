<?php

namespace PowerComponents\LivewirePowerGrid\DataSource\Processors\Database\Handlers;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Arr;
use PowerComponents\LivewirePowerGrid\DataSource\Builders\{Boolean, DatePicker, DateTimePicker, InputText, MultiSelect, Number, Select};
use PowerComponents\LivewirePowerGrid\DataSource\Support\InputOperators;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

class FilterHandler
{
    use InputOperators;

    public function __construct(
        private readonly PowerGridComponent $component
    ) {}

    public function apply(EloquentBuilder|QueryBuilder $query): EloquentBuilder|QueryBuilder
    {
        $filterDefinitions = collect($this->component->filters());

        if ($filterDefinitions->isEmpty() || empty($this->component->filters)) {
            return $query;
        }

        foreach ($this->component->filters as $filterType => $columns) {
            $columns = Arr::dot($columns);

            $newColumns = [];

            foreach ($columns as $key => $value) {
                $parts = explode('.', $key);
                $lastPart = end($parts);

                if (is_numeric($lastPart) && intval($lastPart) == $lastPart) {
                    array_pop($parts);
                    $prefix = implode('.', $parts);

                    if (! isset($newColumns[$prefix])) {
                        $newColumns[$prefix] = [];
                    }

                    $index = intval($lastPart);

                    $newColumns[$prefix][$index] = $value;
                } elseif ($lastPart === 'start' || $lastPart === 'end') {
                    $prefix = implode('.', array_slice($parts, 0, -1));

                    if (! isset($newColumns[$prefix])) {
                        $newColumns[$prefix] = [];
                    }

                    $newColumns[$prefix][$lastPart] = $value;
                } else {
                    $newColumns[$key] = $value;
                }
            }

            foreach ($newColumns as $field => $value) {
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
