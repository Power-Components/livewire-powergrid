<?php

namespace PowerComponents\LivewirePowerGrid\Themes\Components;

class FilterSelect
{
    public string $selectClass = '';

    public string $baseClass = '';

    public string $baseStyle = '';

    public string $view = '';

    public function view(string $view): FilterSelect
    {
        $this->view = $view;

        return $this;
    }

    public function select(string $attrClass = ''): FilterSelect
    {
        $this->selectClass = $attrClass;

        return $this;
    }

    public function base(string $attrClass = '', string $attrStyle = ''): FilterSelect
    {
        $this->baseClass = $attrClass;

        $this->baseStyle = $attrStyle;

        return $this;
    }
}
