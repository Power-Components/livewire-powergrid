<?php

namespace PowerComponents\LivewirePowerGrid\Themes;

abstract class ThemeBase
{
    public string $name;

    protected string $base = "livewire-powergrid::components.frameworks.";

    public static string $scripts = "";

    public static string $styles = "";

    public ?Components\Table $table;

    public ?Components\Checkbox $checkbox;

    public ?Components\Layout $layout;

    public ?Components\Actions $actions;

    public ?Components\Editable $editable;

    public ?Components\Toggleable $toggleable;

    public ?Components\FilterBoolean $filterBoolean;

    public ?Components\FilterSelect $filterSelect;

    public ?Components\FilterDatePicker $filterDatePicker;

    public ?Components\FilterMultiSelect $filterMultiSelect;

    public ?Components\FilterNumber $filterNumber;

    public ?Components\FilterInputText $filterInputText;

    public ?Components\PerPage $perPage;

    public ?Components\Cols $cols;

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

    public function table(): ?Components\Table
    {
        return null;
    }

    public function checkbox(): ?Components\Checkbox
    {
        return null;
    }

    public function perPage(): ?Components\PerPage
    {
        return null;
    }

    public function editable(): ?Components\Editable
    {
        return null;
    }

    public function cols(): ?Components\Cols
    {
        return null;
    }

    public function actions(): ?Components\Actions
    {
        return null;
    }

    public function layout(): ?Components\Layout
    {
        return null;
    }

    public function toggleable(): ?Components\Toggleable
    {
        return null;
    }

    public function filterBoolean(): ?Components\FilterBoolean
    {
        return null;
    }

    public function filterDatePicker(): ?Components\FilterDatePicker
    {
        return null;
    }

    public function filterMultiSelect(): ?Components\FilterMultiSelect
    {
        return null;
    }

    public function filterNumber(): ?Components\FilterNumber
    {
        return null;
    }

    public function filterSelect(): ?Components\FilterSelect
    {
        return null;
    }

    public function filterInputText(): ?Components\FilterInputText
    {
        return null;
    }
}
