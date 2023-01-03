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
            $filters = collect([]);

            /** @var array $filter */
            foreach ($column->filters as $filter) {
                if (str(strval(data_get($filter, 'className')))->contains('FilterDynamic')) {
                    $filterType = strval(data_get($filter, 'filterType'));
                    $dataField  = strval(data_get($filter, 'field'));

                    if (!in_array($filterType, DynamicInput::DYNAMIC_FILTERS)) {
                        throw new Exception('Available options must be included by: \PowerComponents\LivewirePowerGrid\DynamicInput::class');
                    }

                    $explodeFilterType = Str::of($filterType)->explode(',');

                    $filter1 = strval($explodeFilterType->get(0));
                    $type    = strval($explodeFilterType->get(1));

                    /** @phpstan-ignore-next-line */
                    $initial = match ($type) {
                        'array'  => [],
                        'string' => '',
                        'bool'   => 'all',
                    };

                    $this->filters[$filter1][$dataField] = $initial;

                    $filter['attributes'] = [
                        ...(array) data_get($filter, 'attributes'),
                        'wire:model.debounce.600ms' => 'filters.' . $filter1 . '.' . $dataField,
                    ];
                }
                $filters->push($filter);
            }

            data_set($column, 'filters', $filters);

            return $column;
        })->toArray();
    }
}
