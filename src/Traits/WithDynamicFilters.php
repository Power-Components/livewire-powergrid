<?php

namespace PowerComponents\LivewirePowerGrid\Traits;

use Exception;
use Illuminate\Support\{Arr, Str};
use PowerComponents\LivewirePowerGrid\{Column, DynamicInput};

trait WithDynamicFilters
{
    private function initializePropertiesFromDynamicFilters(): void
    {
        $this->columns = collect($this->columns)->transform(function (Column $column) {
            if (Arr::has($column->inputs, 'dynamic')) {
                $filterType = strval(data_get($column->inputs, 'dynamic.filterType'));
                $dataField  = strval(data_get($column->inputs, 'dynamic.dataField'));

                if (!in_array($filterType, DynamicInput::DYNAMIC_FILTERS)) {
                    throw new Exception('Available options must be included by: \PowerComponents\LivewirePowerGrid\DynamicInput::class');
                }

                $filter     = strval(Str::of($filterType)->explode(',')->get(0));
                $type       = strval(Str::of($filterType)->explode(',')->get(1));

                /** @phpstan-ignore-next-line */
                $initial = match ($type) {
                    'array'  => [],
                    'string' => '',
                    'bool'   => 'all',
                };

                $this->filters[$filter][$dataField] = $initial;

                data_set($column->inputs, 'dynamic.attributes', [
                    ...(array) data_get($column->inputs, 'dynamic.attributes'),
                    'wire:model.debounce.500ms' => 'filters.' . $filter . '.' . $dataField,
                ]);
            }

            return $column;
        })->toArray();
    }
}
