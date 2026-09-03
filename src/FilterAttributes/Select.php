<?php

namespace PowerComponents\LivewirePowerGrid\FilterAttributes;

use Illuminate\View\ComponentAttributeBag;
use PowerComponents\LivewirePowerGrid\Support\FilterKey;

class Select
{
    /** @return array{selectAttributes: ComponentAttributeBag} */
    public function __invoke(string $field, string $title, bool $deferred = false): array
    {
        if ($deferred) {
            return [
                'selectAttributes' => new ComponentAttributeBag(
                    FilterKey::draftModel('select.'.FilterKey::encode($field)),
                ),
            ];
        }

        return [
            'selectAttributes' => new ComponentAttributeBag([
                'wire:model' => 'filters.select.'.$field,
                'wire:input.live.debounce.600ms' => 'filterSelect(\''.$field.'\', \''.addslashes($title).'\')',
            ]),
        ];
    }
}
