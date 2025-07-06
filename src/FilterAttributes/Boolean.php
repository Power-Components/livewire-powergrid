<?php

namespace PowerComponents\LivewirePowerGrid\FilterAttributes;

use Illuminate\View\ComponentAttributeBag;

class Boolean
{
    public function __invoke(string $field, string $title): array
    {
        return [
            'selectAttributes' => new ComponentAttributeBag([
                'wire:model'                     => 'filters.boolean.' . $field,
                'wire:input.live.debounce.600ms' => "filterBoolean('{$field}', \$event.target.value, '{$title}')",
            ]),
        ];
    }
}
