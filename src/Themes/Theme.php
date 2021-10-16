<?php

namespace PowerComponents\LivewirePowerGrid\Themes;

use Illuminate\Support\Facades\Facade;
use PowerComponents\LivewirePowerGrid\Themes\Components\Actions;
use PowerComponents\LivewirePowerGrid\Themes\Components\Checkbox;
use PowerComponents\LivewirePowerGrid\Themes\Components\Cols;
use PowerComponents\LivewirePowerGrid\Themes\Components\Editable;
use PowerComponents\LivewirePowerGrid\Themes\Components\FilterBoolean;
use PowerComponents\LivewirePowerGrid\Themes\Components\FilterDatePicker;
use PowerComponents\LivewirePowerGrid\Themes\Components\FilterInputText;
use PowerComponents\LivewirePowerGrid\Themes\Components\FilterMultiSelect;
use PowerComponents\LivewirePowerGrid\Themes\Components\FilterNumber;
use PowerComponents\LivewirePowerGrid\Themes\Components\FilterSelect;
use PowerComponents\LivewirePowerGrid\Themes\Components\Footer;
use PowerComponents\LivewirePowerGrid\Themes\Components\Layout;
use PowerComponents\LivewirePowerGrid\Themes\Components\Table;
use PowerComponents\LivewirePowerGrid\Themes\Components\Toggleable;

/**
 * @method static Table table(string $attrClass, string $attrStyle='')
 * @method static Footer footer()
 * @method static Toggleable toggleable()
 * @method static Layout layout()
 * @method static Cols cols()
 * @method static Actions actions()
 * @method static Checkbox checkbox()
 * @method static Editable editable()
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
