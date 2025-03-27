<?php

namespace PowerComponents\LivewirePowerGrid\FilterAttributes;

use Illuminate\View\ComponentAttributeBag;

class InputText
{
    public function __invoke(string $field, string $title): array
    {
        return [
            'inputAttributes' => new ComponentAttributeBag([
                'wire:model'                     => 'filters.input_text.' . $field,
                'wire:input.live.debounce.600ms' => "filterInputText('{$field}', \$event.target.value, '{$title}')",
            ]),
            'selectAttributes' => new ComponentAttributeBag([
                'wire:model'                     => 'filters.input_text_options.' . $field,
                'wire:input.live.debounce.600ms' => "filterInputTextOptions('{$field}', \$event.target.value, '{$title}')",
            ]),
        ];
    }
}
