<?php

namespace PowerComponents\LivewirePowerGrid\Themes\Components;

class Editable
{
    public string $spanClass = '';

    public string $view = '';

    public string $buttonClass = '';

    public string $inputClass = '';

    public string $baseClass = '';

    public string $baseStyle = '';

    public function view(string $view): Editable
    {
        $this->view = $view;

        return $this;
    }

    public function base(string $attrClass = '', string $attrStyle = ''): Editable
    {
        $this->baseClass = $attrClass;

        $this->baseStyle = $attrStyle;

        return $this;
    }

    public function span(string $attrClass): Editable
    {
        $this->spanClass = $attrClass;

        return $this;
    }

    public function button(string $attrClass): Editable
    {
        $this->buttonClass = $attrClass;

        return $this;
    }

    public function input(string $attrClass): Editable
    {
        $this->inputClass = $attrClass;

        return $this;
    }
}
