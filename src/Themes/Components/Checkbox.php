<?php

namespace PowerComponents\LivewirePowerGrid\Themes\Components;

class Checkbox
{
    public string $labelClass = '';

    public string $thClass = '';

    public string $inputClass = '';

    public string $thStyle = '';

    public string $labelStyle = '';

    public string $inputStyle = '';

    public string $divClass = '';

    public function th(string $attrClass, string $attrStyle = ''): Checkbox
    {
        $this->thClass = $attrClass;
        $this->thStyle = $attrStyle;

        return $this;
    }

    public function label(string $attrClass, string $attrStyle = ''): Checkbox
    {
        $this->labelClass = $attrClass;
        $this->labelStyle = $attrStyle;

        return $this;
    }

    public function input(string $attrClass, string $attrStyle = ''): Checkbox
    {
        $this->inputClass = $attrClass;
        $this->inputStyle = $attrStyle;

        return $this;
    }

    public function div(string $attrClass): Checkbox
    {
        $this->divClass = $attrClass;

        return $this;
    }
}
