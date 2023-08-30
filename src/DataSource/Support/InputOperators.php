<?php

namespace PowerComponents\LivewirePowerGrid\DataSource\Support;

use PowerComponents\LivewirePowerGrid\Components\Filters\FilterInputText;

trait InputOperators
{
    public function validateInputTextOptions(array $filter, string $field): string
    {
        /** @var array|string $selected */
        $selected = data_get($filter, "input_text_options.$field");

        if (is_array($selected)) {
            $selected = collect($selected)->values()[0];
        }

        return in_array(strval(
            $selected
        ), FilterInputText::getInputTextOperators()) ?
            strval($selected) : 'contains';
    }
}
