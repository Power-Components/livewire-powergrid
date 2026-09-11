<?php

namespace PowerComponents\LivewirePowerGrid\FilterAttributes;

use Illuminate\View\ComponentAttributeBag;
use PowerComponents\LivewirePowerGrid\Support\FilterKey;

class Select
{
    /** @return array{selectAttributes: ComponentAttributeBag} */
    public function __invoke(string $field, string $title, bool $deferred = false): array
    {
        $key = FilterKey::modelKey($field);

        if ($deferred) {
            return [
                'selectAttributes' => new ComponentAttributeBag(FilterKey::draftModel($key.'.value')),
            ];
        }

        return [
            'selectAttributes' => new ComponentAttributeBag(
                FilterKey::liveModel($key.'.value', debounce: false),
            ),
        ];
    }
}
