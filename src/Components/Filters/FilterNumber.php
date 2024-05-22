<?php

namespace PowerComponents\LivewirePowerGrid\Components\Filters;

use Illuminate\Support\Js;
use Illuminate\View\ComponentAttributeBag;

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
        return collect()
            ->put('inputStartAttributes', new ComponentAttributeBag([
                'wire:model'                     => 'filters.number.' . $field . '.start',
                'wire:input.live.debounce.600ms' => 'filterNumberStart(\'' . $field . '\', ' . Js::from($filter) . ', $event.target.value)',
            ]))
            ->put('inputEndAttributes', new ComponentAttributeBag([
                'wire:model'                     => 'filters.number.' . $field . '.end',
                'wire:input.live.debounce.600ms' => 'filterNumberEnd(\'' . $field . '\', ' . Js::from($filter) . ', $event.target.value)',
            ]))
            ->toArray();
    }
}
