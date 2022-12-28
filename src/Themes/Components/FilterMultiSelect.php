<?php

namespace PowerComponents\LivewirePowerGrid\Themes\Components;

class FilterMultiSelect
{
    public string $selectClass = '';

    public string $view = '';

    public string $baseClass = '';

    public string $baseStyle = '';

    public function base(string $attrClass = '', string $attrStyle = ''): FilterMultiSelect
    {
        $this->baseClass = $attrClass;

        $this->baseStyle = $attrStyle;

        return $this;
    }

    public function select(string $attrClass = ''): FilterMultiSelect
    {
        $this->selectClass = $attrClass;

        return $this;
    }

    public function view(string $view): FilterMultiSelect
    {
        $this->view = $view;

        return $this;
    }
}
