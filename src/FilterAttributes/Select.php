<?php

namespace PowerComponents\LivewirePowerGrid\FilterAttributes;

use Illuminate\View\ComponentAttributeBag;

class Select
{
    public function __invoke(string $field, string $title): array
    {
        return [
            'selectAttributes' => new ComponentAttributeBag([
                'wire:model'                     => 'filters.select.' . $field,
                'wire:input.live.debounce.600ms' => 'filterSelect(\'' . $field . '\', \'' . $title . '\')',
            ]),
        ];
    }
}
