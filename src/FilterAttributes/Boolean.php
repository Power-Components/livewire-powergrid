<?php

namespace PowerComponents\LivewirePowerGrid\FilterAttributes;

class Boolean
{
    public function __invoke(string $field, string $title): array
    {
        return [
            'selectAttributes' => [
                'wire:model'                     => 'filters.boolean.' . $field,
                'wire:input.live.debounce.600ms' => "filterBoolean('{$field}', \$event.target.value, '{$title}')",
            ],
        ];
    }
}
