<?php

namespace PowerComponents\LivewirePowerGrid\Themes\Components;

class FilterDatePicker
{
    public string $inputClass = '';

    public string $inputStyle = '';

    public string $divClassNotInline = '';

    public string $divClassInline = '';

    public function input(string $attrClass, string $attrStyle = ''): FilterDatePicker
    {
        $this->inputClass = $attrClass;

        $this->inputStyle = $attrStyle;

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
