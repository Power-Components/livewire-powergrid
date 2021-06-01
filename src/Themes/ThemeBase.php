<?php

namespace PowerComponents\LivewirePowerGrid\Themes;

use PowerComponents\LivewirePowerGrid\Themes\Components\{Checkbox,
    Editable,
    FilterBoolean,
    FilterDatePicker,
    FilterInputText,
    FilterMultiSelect,
    FilterNumber,
    FilterSelect,
    PerPage,
    Table,
    Toggleable};

abstract class ThemeBase
{
    public static string $scripts;

    public static string $styles;

    public ?Table $table;

    public ?Checkbox $checkbox;

    public ?Editable $editable;

    public ?Toggleable $toggleable;

    public ?FilterBoolean $filterBoolean;

    public ?FilterSelect $filterSelect;

    public ?FilterDatePicker $filterDatePicker;

    public ?FilterMultiSelect $filterMultiSelect;

    public ?FilterNumber $filterNumber;

    public ?FilterInputText $filterInputText;

    public ?string $tableBaseView;

    public ?PerPage $perPage;

    public function apply(): ThemeBase
    {
        $this->table             = $this->table();
        $this->perPage           = $this->perPage();
        $this->editable          = $this->editable();
        $this->toggleable        = $this->toggleable();
        $this->checkbox          = $this->checkbox();
        $this->filterBoolean     = $this->filterBoolean();
        $this->filterDatePicker  = $this->filterDatePicker();
        $this->filterMultiSelect = $this->filterMultiSelect();
        $this->filterNumber      = $this->filterNumber();
        $this->filterSelect      = $this->filterSelect();
        $this->filterInputText   = $this->filterInputText();
        $this->tableBaseView     = $this->tableBaseView();
        return $this;
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

    public function editable(): ?Editable
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

    public function tableBaseView()
    {
        return null;
    }
}
