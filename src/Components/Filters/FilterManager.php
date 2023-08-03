<?php

namespace PowerComponents\LivewirePowerGrid\Components\Filters;

class FilterManager
{
    public function multiSelect(string $column, ?string $field = null): FilterMultiSelect
    {
        return new FilterMultiSelect($column, $field);
    }

    public function multiSelectAsync(string $column, ?string $field = null): FilterMultiSelectAsync
    {
        return new FilterMultiSelectAsync($column, $field);
    }

    public function inputText(string $column, ?string $field = null): FilterInputText
    {
        return new FilterInputText($column, $field);
    }

    public function select(string $column, ?string $field = null): FilterSelect
    {
        return new FilterSelect($column, $field);
    }

    public function enumSelect(string $column, ?string $field = null): FilterEnumSelect
    {
        return new FilterEnumSelect($column, $field);
    }

    public function number(string $column, ?string $field = null): FilterNumber
    {
        return new FilterNumber($column, $field);
    }

    public function dynamic(string $column, ?string $field = null): FilterDynamic
    {
        return new FilterDynamic($column, $field);
    }

    public function datepicker(string $column, ?string $field = null): FilterDatePicker
    {
        return new FilterDatePicker($column, $field);
    }

    public function datetimepicker(string $column, ?string $field = null): FilterDateTimePicker
    {
        return new FilterDateTimePicker($column, $field);
    }

    public function boolean(string $column, ?string $field = null): FilterBoolean
    {
        return new FilterBoolean($column, $field);
    }
}
