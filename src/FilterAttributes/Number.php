<?php

namespace PowerComponents\LivewirePowerGrid\FilterAttributes;

class Number
{
    public function __invoke(string $field, array $filter): array
    {
        return [
            'inputStartAttributes' => [
                'wire:model'                     => "filters.number.{$field}.start",
                'wire:input.live.debounce.600ms' => "filterNumberStart('{$field}', " . json_encode($filter) . ", \$event.target.value)",
            ],
            'inputEndAttributes' => [
                'wire:model'                     => "filters.number.{$field}.end",
                'wire:input.live.debounce.600ms' => "filterNumberEnd('{$field}', " . json_encode($filter) . ", \$event.target.value)",
            ],
        ];
    }
}
