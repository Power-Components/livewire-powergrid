<?php

namespace PowerComponents\LivewirePowerGrid\Components\Filters;

use Illuminate\Support\Js;
use Illuminate\View\ComponentAttributeBag;

class FilterNumber extends FilterBase
{
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
                'wire:input.live.debounce.800ms' => 'filterNumberStart(\'' . $field . '\', ' . Js::from($filter) . ', $event.target.value)',
            ]))
            ->put('inputEndAttributes', new ComponentAttributeBag([
                'wire:input.live.debounce.800ms' => 'filterNumberEnd(\'' . $field . '\', ' . Js::from($filter) . ', $event.target.value)',
            ]))
            ->toArray();
    }
}
