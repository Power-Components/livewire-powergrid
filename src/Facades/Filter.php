<?php

namespace PowerComponents\LivewirePowerGrid\Facades;

use Illuminate\Support\Facades\Facade;
use PowerComponents\LivewirePowerGrid\Components\Filters\{FilterBoolean, FilterDatePicker, FilterDateTimePicker, FilterDynamic, FilterEnumSelect, FilterInputText, FilterMultiSelect, FilterMultiSelectAsync, FilterNumber, FilterSelect};

/**
 * @method static FilterMultiSelect multiSelect(string $column, ?string $field = null)
 * @method static FilterMultiSelectAsync multiSelectAsync(string $column, ?string $field = null)
 * @method static FilterInputText inputText(string $column, ?string $field = null)
 * @method static FilterSelect select(string $column, ?string $field = null)
 * @method static FilterEnumSelect enumSelect(string $column, ?string $field = null)
 * @method static FilterNumber number(string $column, ?string $field = null)
 * @method static FilterDynamic dynamic(string $column, ?string $field = null)
 * @method static FilterDatePicker datepicker(string $column, ?string $field = null)
 * @method static FilterDateTimePicker datetimepicker(string $column, ?string $field = null)
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
