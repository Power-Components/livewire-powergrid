<?php

namespace PowerComponents\LivewirePowerGrid\Themes\Components;

class FilterDatePicker
{
    public string $inputClass = '';

    public string $divClassNotInline = '';

    public string $divClassInline = '';

    public function input(string $inputClass): FilterDatePicker
    {
        $this->inputClass = $inputClass;

        return $this;
    }

    public function divNotInline(string $divClassNotInline): FilterDatePicker
    {
        $this->divClassNotInline = $divClassNotInline;

        return $this;
    }

    public function divInline(string $divClassInline): FilterDatePicker
    {
        $this->divClassInline = $divClassInline;

        return $this;
    }
}
