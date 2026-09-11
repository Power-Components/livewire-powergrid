<?php

namespace PowerComponents\LivewirePowerGrid\FilterAttributes;

use Illuminate\View\ComponentAttributeBag;
use PowerComponents\LivewirePowerGrid\Support\FilterKey;

class Number
{
    /**
     * @param  array<string, mixed>  $filter
     * @return array{inputStartAttributes: ComponentAttributeBag, inputEndAttributes: ComponentAttributeBag}
     */
    public function __invoke(string $field, array $filter, bool $deferred = false): array
    {
        $key = FilterKey::modelKey($field);

        if ($deferred) {
            return [
                'inputStartAttributes' => new ComponentAttributeBag(FilterKey::draftModel($key.'.value.start')),
                'inputEndAttributes' => new ComponentAttributeBag(FilterKey::draftModel($key.'.value.end')),
            ];
        }

        return [
            'inputStartAttributes' => new ComponentAttributeBag(FilterKey::liveModel($key.'.value.start')),
            'inputEndAttributes' => new ComponentAttributeBag(FilterKey::liveModel($key.'.value.end')),
        ];
    }
}
