<?php

namespace PowerComponents\LivewirePowerGrid\Themes;

use Illuminate\Support\Facades\Facade;
use PowerComponents\LivewirePowerGrid\Themes\Components\{Actions,
    Checkbox,
    ClickToCopy,
    Cols,
    Editable,
    FilterBoolean,
    FilterDatePicker,
    FilterInputText,
    FilterMultiSelect,
    FilterNumber,
    FilterSelect,
    Footer,
    Layout,
    Row,
    Table,
    Toggleable};

/**
 * @method static Table table(string $attrClass, string $attrStyle='')
 * @method static Footer footer()
 * @method static Toggleable toggleable()
 * @method static Layout layout()
 * @method static Cols cols()
 * @method static Row row()
 * @method static Actions actions()
 * @method static Checkbox checkbox()
 * @method static Editable editable()
 * @method static ClickToCopy clickToCopy()
 * @method static FilterBoolean filterBoolean()
 * @method static FilterDatePicker filterDatePicker()
 * @method static FilterMultiSelect filterMultiSelect()
 * @method static FilterNumber filterNumber()
 * @method static FilterSelect filterSelect()
 * @method static FilterInputText filterInputText()
 * @see \PowerComponents\LivewirePowerGrid\Themes\ThemeManager
 */
class Theme extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return 'theme';
    }
}
