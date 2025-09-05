<?php

namespace PowerComponents\LivewirePowerGrid\Components\Filters;

use PowerComponents\LivewirePowerGrid\FilterAttributes\Number;

class FilterNumber extends FilterBase
{
    public string $key = 'number';

    public string $thousands = '';

    public string $decimal = '';

    public array $placeholder = [];

    public function thousands(string $thousands): FilterNumber
    {
        $this->thousands = $thousands;

        return $this;
    }

    public function decimal(string $decimal): FilterNumber
    {
        $this->decimal = $decimal;

        return $this;
    }

    public function placeholder(string $min, string $max): FilterNumber
    {
        $this->placeholder = [
            'min' => $min,
            'max' => $max,
        ];

        return $this;
    }

    public static function getWireAttributes(string $field, array $filter): array
    {
        $configAttributes = config('livewire-powergrid.filter_attributes.number', Number::class);

        /** @var callable $class */
        $class = new $configAttributes();

        return $class($field, $filter);
    }
}
