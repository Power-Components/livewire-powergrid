<?php

namespace PowerComponents\LivewirePowerGrid\Themes;

use PowerComponents\LivewirePowerGrid\Themes\Components\{Checkbox,
    Editable,
    Toggleable,
    FilterBoolean,
    FilterDatePicker,
    FilterInputText,
    FilterMultiSelect,
    FilterNumber,
    FilterSelect,
    PerPage,
    Table};

class ThemeManager
{
    public function table(string $attrClass, string $attrStyle=''): Table
    {
        return new Table($attrClass, $attrStyle);
    }

    public function perPage(): PerPage
    {
        return new PerPage();
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
