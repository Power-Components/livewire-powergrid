<?php

namespace PowerComponents\LivewirePowerGrid\Helpers;

trait InputOperators
{
    public array $inputTextOperators;

    public function validateInputTextOptions(array $filter, string $field): string
    {
        return in_array(strval(data_get($filter, "input_text_options.$field")), $this->inputTextOperators) ?
            strval(data_get($filter, "input_text_options.$field")) : "contains";
    }
}
