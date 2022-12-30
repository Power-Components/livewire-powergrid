<?php

namespace PowerComponents\LivewirePowerGrid\Filters;

class FilterManager
{
    public function multiSelect(string $column, string $field): FilterMultiSelect
    {
        return new FilterMultiSelect($column, $field);
    }

    public function multiSelectAsync(string $column, string $field): FilterMultiSelectAsync
    {
        return new FilterMultiSelectAsync($column, $field);
    }

    public function inputText(string $column, string $field): FilterInputText
    {
        return new FilterInputText($column, $field);
    }
}
