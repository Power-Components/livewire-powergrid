<?php

namespace PowerComponents\LivewirePowerGrid;

final class DynamicInput
{
    public const FILTER_SELECT = 'select,string';

    public const FILTER_MULTI_SELECT = 'multi_select,array';

    public const FILTER_BOOLEAN = 'boolean,bool';

    public const FILTER_INPUT_TEXT_CONTAINS = 'input_text_contains,string';

    public const FILTER_NUMBER = 'number,string';

    public const FILTER_DATE_PICKER = 'date_picker,string';

    public const DYNAMIC_FILTERS = [
        self::FILTER_SELECT,
        self::FILTER_MULTI_SELECT,
        self::FILTER_BOOLEAN,
        self::FILTER_INPUT_TEXT_CONTAINS,
        self::FILTER_NUMBER,
        self::FILTER_DATE_PICKER,
    ];
}
