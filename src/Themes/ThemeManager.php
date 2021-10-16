<?php

namespace PowerComponents\LivewirePowerGrid\Themes;

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

class ThemeManager
{
    public function table(string $attrClass, string $attrStyle = ''): Table
    {
        return new Table($attrClass, $attrStyle);
    }

    public function actions(): Actions
    {
        return new Actions();
    }

    public function cols(): Cols
    {
        return new Cols();
    }

    public function footer(): Footer
    {
        return new Footer();
    }

    public function toggleable(): Toggleable
    {
        return new Toggleable();
    }

    public function checkbox(): Checkbox
    {
        return new Checkbox();
    }

    public function editable(): Editable
    {
        return new Editable();
    }

    public function layout(): Layout
    {
        return new Layout();
    }

    public function filterBoolean(): FilterBoolean
    {
        return new FilterBoolean();
    }

    public function filterDatePicker(): FilterDatePicker
    {
        return new FilterDatePicker();
    }

    public function filterMultiSelect(): FilterMultiSelect
    {
        return new FilterMultiSelect();
    }

    public function filterNumber(): FilterNumber
    {
        return new FilterNumber();
    }

    public function filterSelect(): FilterSelect
    {
        return new FilterSelect();
    }

    public function filterInputText(): FilterInputText
    {
        return new FilterInputText();
    }
}
