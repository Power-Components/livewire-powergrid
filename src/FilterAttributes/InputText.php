<?php

namespace PowerComponents\LivewirePowerGrid\FilterAttributes;

use Illuminate\View\ComponentAttributeBag;
use PowerComponents\LivewirePowerGrid\Support\FilterKey;

class InputText
{
    /** @return array{inputAttributes: ComponentAttributeBag, selectAttributes: ComponentAttributeBag} */
    public function __invoke(string $field, string $title, bool $deferred = false): array
    {
        $key = FilterKey::modelKey($field);

        if ($deferred) {
            return [
                'inputAttributes' => new ComponentAttributeBag(FilterKey::draftModel($key.'.value')),
                'selectAttributes' => new ComponentAttributeBag(FilterKey::draftModel($key.'.op')),
            ];
        }

        return [
            'inputAttributes' => new ComponentAttributeBag(FilterKey::liveModel($key.'.value')),
            'selectAttributes' => new ComponentAttributeBag(FilterKey::liveModel($key.'.op')),
        ];
    }
}
