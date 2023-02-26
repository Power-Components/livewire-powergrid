<?php

namespace PowerComponents\LivewirePowerGrid\Filters;

class FilterInputText extends FilterBase
{
    public array $operators = [];

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
//        if (!in_array('contains', $value)) {
//            $value[] = 'contains';
//        }

        $this->operators = $value;

        return $this;
    }

    public static function getInputTextOperators(): array
    {
        return self::$inputTextOptions;
    }
}
