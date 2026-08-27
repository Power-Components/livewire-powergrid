<?php

namespace PowerComponents\LivewirePowerGrid\FilterAttributes;

use Illuminate\View\ComponentAttributeBag;

class InputText
{
    /** @return array{inputAttributes: ComponentAttributeBag, selectAttributes: ComponentAttributeBag} */
    public function __invoke(string $field, string $title, bool $deferred = false): array
    {
        if ($deferred) {
            return [
                'inputAttributes' => new ComponentAttributeBag([
                    'wire:model' => 'draftFilters.input_text.'.$field,
                ]),
                'selectAttributes' => new ComponentAttributeBag([
                    'wire:model' => 'draftFilters.input_text_options.'.$field,
                ]),
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
