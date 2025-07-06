<?php

namespace PowerComponents\LivewirePowerGrid\Components\Filters;

use PowerComponents\LivewirePowerGrid\FilterAttributes\InputText;

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
        $configAttributes = config('livewire-powergrid.filter_attributes.input_text', InputText::class);

        /** @var callable $class */
        $class = new $configAttributes();

        return $class($field, $title);
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
