<?php

namespace PowerComponents\LivewirePowerGrid\FilterAttributes;

use Illuminate\Support\Js;
use Illuminate\View\ComponentAttributeBag;
use PowerComponents\LivewirePowerGrid\Support\FilterKey;

class Number
{
    /**
     * @param  array<string, mixed>  $filter
     * @return array{inputStartAttributes: ComponentAttributeBag, inputEndAttributes: ComponentAttributeBag}
     *
     * @throws \JsonException
     */
    public function __invoke(string $field, array $filter, bool $deferred = false): array
    {
        if ($deferred) {
            $key = FilterKey::encode($field);

            return [
                'inputStartAttributes' => new ComponentAttributeBag(FilterKey::draftModel("number.{$key}.start")),
                'inputEndAttributes' => new ComponentAttributeBag(FilterKey::draftModel("number.{$key}.end")),
            ];
        }

        return [
            'inputStartAttributes' => new ComponentAttributeBag([
                'wire:model' => "filters.number.{$field}.start",
                'wire:input.live.debounce.600ms' => 'filterNumberStart(\''.$field.'\', '.Js::from($filter).', $event.target.value)',
            ]),
            'inputEndAttributes' => new ComponentAttributeBag([
                'wire:model' => "filters.number.{$field}.end",
                'wire:input.live.debounce.600ms' => 'filterNumberEnd(\''.$field.'\', '.Js::from($filter).', $event.target.value)',
            ]),
        ];
    }
}
