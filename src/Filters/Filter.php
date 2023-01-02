<?php

namespace PowerComponents\LivewirePowerGrid\Filters;

use Illuminate\Support\Facades\Facade;

/**
 * @method static FilterMultiSelect multiSelect(string $column, ?string $field = null)
 * @method static FilterMultiSelectAsync multiSelectAsync(string $column, ?string $field = null)
 * @method static FilterInputText inputText(string $column, ?string $field = null)
 * @method static FilterSelect select(string $column, ?string $field = null)
 * @method static FilterEnumSelect enumSelect(string $column, ?string $field = null)
 * @method static FilterNumber number(string $column, ?string $field = null)
 * @method static FilterDynamic dynamic(string $column, ?string $field = null)
 * @method static FilterDatePicker datepicker(string $column, ?string $field = null)
 * @method static FilterBoolean boolean(string $column, ?string $field = null)
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
