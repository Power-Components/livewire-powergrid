<?php

namespace PowerComponents\LivewirePowerGrid\Themes\Components;

class FilterInputText
{
    public string $view = '';

    public string $inputClass = '';

    public string $selectClass = '';

    public string $baseClass = '';

    public string $baseStyle = '';

    public function base(string $attrClass = '', string $attrStyle = ''): FilterInputText
    {
        $this->baseClass = $attrClass;

        $this->baseStyle = $attrStyle;

        return $this;
    }

    public function view(string $view): FilterInputText
    {
        $this->view = $view;

        return $this;
    }

    public function input(string $attrClass = ''): FilterInputText
    {
        $this->inputClass = $attrClass;

        return $this;
    }

    public function select(string $attrClass = ''): FilterInputText
    {
        $this->selectClass = $attrClass;

        return $this;
    }
}
