<?php

namespace PowerComponents\LivewirePowerGrid\Themes\Components;

class FilterNumber
{
    public string $view = '';

    public string $inputClass = '';

    public string $inputStyle = '';

    public string $baseClass = '';

    public string $baseStyle = '';

    public function base(string $attrClass = '', string $attrStyle = ''): FilterNumber
    {
        $this->baseClass = $attrClass;

        $this->baseStyle = $attrStyle;

        return $this;
    }

    public function view(string $view): FilterNumber
    {
        $this->view    = $view;

        return $this;
    }

    public function input(string $attrClass = '', string $attrStyle = ''): FilterNumber
    {
        $this->inputClass = $attrClass;

        $this->inputStyle = $attrStyle;

        return $this;
    }
}
