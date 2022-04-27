<?php

namespace PowerComponents\LivewirePowerGrid\Themes\Components;

class FilterBoolean
{
    public string $selectClass = '';

    public string $selectStyle = '';

    public string $view = '';

    public string $baseClass = '';

    public string $baseStyle = '';

    public function base(string $attrClass = '', string $attrStyle = ''): FilterBoolean
    {
        $this->baseClass = $attrClass;

        $this->baseStyle = $attrStyle;

        return $this;
    }

    public function view(string $view): FilterBoolean
    {
        $this->view    = $view;

        return $this;
    }

    public function select(string $attrClass, string $attrStyle = ''): FilterBoolean
    {
        $this->selectClass = $attrClass;

        $this->selectStyle = $attrStyle;

        return $this;
    }
}
