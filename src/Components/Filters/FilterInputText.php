<?php

namespace PowerComponents\LivewirePowerGrid\Components\Filters;

use Illuminate\View\ComponentAttributeBag;

class FilterInputText extends FilterBase
{
    public string $key = 'input_text';

    public array $operators = [];

    public string $placeholder = '';

    private static array $inputTextOptions = [
        'contains',
        'contains_not',
        'is',
        'is_not',
        'starts_with',
        'ends_with',
        'is_empty',
        'is_not_empty',
        'is_null',
        'is_not_null',
        'is_blank',
        'is_not_blank',
    ];

    public function operators(array $value = []): FilterInputText
    {
        if (!in_array('contains', $value)) {
            $value[] = 'contains';
        }

        $this->operators = $value;

        return $this;
    }

    public static function getWireAttributes(string $field, string $title): array
    {
        return collect()
            ->put('selectAttributes', new ComponentAttributeBag([
                'wire:model'                     => 'filters.input_text_options.' . $field,
                'wire:input.live.debounce.600ms' => 'filterInputTextOptions(\'' . $field . '\', $event.target.value, \'' . $title . '\')',
            ]))
            ->put('inputAttributes', new ComponentAttributeBag([
                'wire:model'                     => 'filters.input_text.' . $field,
                'wire:input.live.debounce.600ms' => 'filterInputText(\'' . $field . '\', $event.target.value, \'' . $title . '\')',
            ]))
            ->toArray();
    }

    public function placeholder(string $placeholder): FilterInputText
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    public static function getInputTextOperators(): array
    {
        return self::$inputTextOptions;
    }
}
