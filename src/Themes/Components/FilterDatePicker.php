<?php

namespace PowerComponents\LivewirePowerGrid\Themes\Components;

class FilterDatePicker
{
    public string $inputClass = '';

    public string $inputStyle = '';

    public string $view = '';

    public string $baseClass = '';

    public string $baseStyle = '';

    public function base(string $attrClass = '', string $attrStyle = ''): FilterDatePicker
    {
        $this->baseClass = $attrClass;

        $this->baseStyle = $attrStyle;

        return $this;
    }

    public function view(string $view): FilterDatePicker
    {
        $this->view = $view;

        return $this;
    }

    public function input(string $attrClass, string $attrStyle = ''): FilterDatePicker
    {
        $this->inputClass = $attrClass;

        $this->inputStyle = $attrStyle;

        return $this;
    }
}
