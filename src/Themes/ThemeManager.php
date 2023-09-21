<?php

namespace PowerComponents\LivewirePowerGrid\Themes;

use PowerComponents\LivewirePowerGrid\Themes\Components\{Actions,
    Checkbox,
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
    Radio,
    SearchBox,
    Table,
    Toggleable};

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

    public function radio(): Radio
    {
        return new Radio();
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

    public function searchBox(): SearchBox
    {
        return new SearchBox();
    }
}
