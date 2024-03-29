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

/** @codeCoverageIgnore */
abstract class AbstractTheme
{
    public Table $table;

    public Checkbox $checkbox;

    public Radio $radio;

    public Layout $layout;

    public Actions $actions;

    public Editable $editable;

    public Toggleable $toggleable;

    public FilterBoolean $filterBoolean;

    public FilterSelect $filterSelect;

    public FilterDatePicker $filterDatePicker;

    public FilterMultiSelect $filterMultiSelect;

    public FilterNumber $filterNumber;

    public FilterInputText $filterInputText;

    public Footer $footer;

    public Cols $cols;

    public SearchBox $searchBox;

    public function table(): Table
    {
        return Theme::table('');
    }

    public function checkbox(): Checkbox
    {
        return Theme::checkbox();
    }

    public function radio(): Radio
    {
        return Theme::radio();
    }

    public function footer(): Footer
    {
        return Theme::footer();
    }

    public function editable(): Editable
    {
        return Theme::editable();
    }

    public function cols(): Cols
    {
        return Theme::cols();
    }

    public function actions(): Actions
    {
        return Theme::actions();
    }

    public function layout(): ?Components\Layout
    {
        return Theme::layout();
    }

    public function toggleable(): Toggleable
    {
        return Theme::toggleable();
    }

    public function filterBoolean(): FilterBoolean
    {
        return Theme::filterBoolean();
    }

    public function filterDatePicker(): FilterDatePicker
    {
        return Theme::filterDatePicker();
    }

    public function filterMultiSelect(): FilterMultiSelect
    {
        return Theme::filterMultiSelect();
    }

    public function filterNumber(): FilterNumber
    {
        return Theme::filterNumber();
    }

    public function filterSelect(): FilterSelect
    {
        return Theme::filterSelect();
    }

    public function filterInputText(): FilterInputText
    {
        return Theme::filterInputText();
    }

    public function searchBox(): SearchBox
    {
        return Theme::searchBox();
    }
}
