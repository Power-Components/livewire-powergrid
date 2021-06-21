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
    Layout,
    PerPage,
    Table,
    Toggleable};

abstract class ThemeBase
{
    public static string $scripts;

    public static string $styles;

    public ?Table $table;

    public ?Checkbox $checkbox;

    public ?Layout $layout;

    public ?Actions $actions;

    public ?Editable $editable;

    public ?Toggleable $toggleable;

    public ?FilterBoolean $filterBoolean;

    public ?FilterSelect $filterSelect;

    public ?FilterDatePicker $filterDatePicker;

    public ?FilterMultiSelect $filterMultiSelect;

    public ?FilterNumber $filterNumber;

    public ?FilterInputText $filterInputText;

    public ?PerPage $perPage;

    public ?Cols $cols;

    public static string $paginationTheme;

    public function apply(): ThemeBase
    {
        $this->table             = $this->table();
        $this->perPage           = $this->perPage();
        $this->cols              = $this->cols();
        $this->editable          = $this->editable();
        $this->layout            = $this->layout();
        $this->toggleable        = $this->toggleable();
        $this->actions           = $this->actions();
        $this->checkbox          = $this->checkbox();
        $this->filterBoolean     = $this->filterBoolean();
        $this->filterDatePicker  = $this->filterDatePicker();
        $this->filterMultiSelect = $this->filterMultiSelect();
        $this->filterNumber      = $this->filterNumber();
        $this->filterSelect      = $this->filterSelect();
        $this->filterInputText   = $this->filterInputText();

        return $this;
    }

    public static function paginationTheme(): string
    {
        return self::$paginationTheme;
    }

    public static function scripts(): string
    {
        return self::$scripts;
    }

    public static function styles(): string
    {
        return self::$styles;
    }

    public function table(): ?Table
    {
        return null;
    }

    public function checkbox(): ?Checkbox
    {
        return null;
    }

    public function perPage(): ?PerPage
    {
        return null;
    }

    public function editable(): ?Editable
    {
        return null;
    }

    public function cols(): ?Cols
    {
        return null;
    }

    public function actions(): ?Actions
    {
        return null;
    }

    public function layout(): ?Layout
    {
        return null;
    }

    public function toggleable(): ?Toggleable
    {
        return null;
    }

    public function filterBoolean(): ?FilterBoolean
    {
        return null;
    }

    public function filterDatePicker(): ?FilterDatePicker
    {
        return null;
    }

    public function filterMultiSelect(): ?FilterMultiSelect
    {
        return null;
    }

    public function filterNumber(): ?FilterNumber
    {
        return null;
    }

    public function filterSelect(): ?FilterSelect
    {
        return null;
    }

    public function filterInputText(): ?FilterInputText
    {
        return null;
    }
}
