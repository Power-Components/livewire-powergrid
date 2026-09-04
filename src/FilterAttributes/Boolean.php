<?php

namespace PowerComponents\LivewirePowerGrid\FilterAttributes;

use Illuminate\View\ComponentAttributeBag;
use PowerComponents\LivewirePowerGrid\Support\FilterKey;

class Boolean
{
    /** @return array{selectAttributes: ComponentAttributeBag} */
    public function __invoke(string $field, string $title, bool $deferred = false): array
    {
        if ($deferred) {
            return [
                'selectAttributes' => new ComponentAttributeBag(
                    FilterKey::draftModel('boolean.'.FilterKey::encode($field)),
                ),
            ];
        }

        return [
            'selectAttributes' => new ComponentAttributeBag([
                'wire:model' => 'filters.boolean.'.$field,
                'wire:input.live.debounce.600ms' => "filterBoolean('{$field}', \$event.target.value, '{$title}')",
            ]),
        ];
    }
}
