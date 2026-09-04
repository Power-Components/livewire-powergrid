<?php

namespace PowerComponents\LivewirePowerGrid\FilterAttributes;

use Illuminate\View\ComponentAttributeBag;
use PowerComponents\LivewirePowerGrid\Support\FilterKey;

class InputText
{
    /** @return array{inputAttributes: ComponentAttributeBag, selectAttributes: ComponentAttributeBag} */
    public function __invoke(string $field, string $title, bool $deferred = false): array
    {
        if ($deferred) {
            $key = FilterKey::encode($field);

            return [
                'inputAttributes' => new ComponentAttributeBag(FilterKey::draftModel('input_text.'.$key)),
                'selectAttributes' => new ComponentAttributeBag(FilterKey::draftModel('input_text_options.'.$key)),
            ];
        }

        return [
            'inputAttributes' => new ComponentAttributeBag([
                'wire:model' => 'filters.input_text.'.$field,
                'wire:input.live.debounce.600ms' => "filterInputText('{$field}', \$event.target.value, '{$title}')",
            ]),
            'selectAttributes' => new ComponentAttributeBag([
                'wire:model' => 'filters.input_text_options.'.$field,
                'wire:input.live.debounce.600ms' => "filterInputTextOptions('{$field}', \$event.target.value, '{$title}')",
            ]),
        ];
    }
}
