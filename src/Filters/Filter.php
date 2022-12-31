<?php

namespace PowerComponents\LivewirePowerGrid\Filters;

use Illuminate\Support\Facades\Facade;

/**
 * @method static FilterMultiSelect multiSelect(string $column, string $field)
 * @method static FilterMultiSelectAsync multiSelectAsync(string $column, string $field)
 * @method static FilterInputText inputText(string $column, string $field)
 * @method static FilterSelect select(string $column, string $field)
 * @method static FilterNumber number(string $column, string $field)
 *
 * @see FilterManager
 */
class Filter extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return 'filter';
    }
}
