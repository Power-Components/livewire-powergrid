<?php

namespace PowerComponents\LivewirePowerGrid\Themes\Components;

class Radio
{
    public string $labelClass = '';

    public string $thClass = '';

    public string $inputClass = '';

    public string $thStyle = '';

    public string $labelStyle = '';

    public string $inputStyle = '';

    public string $divClass = '';

    public function th(string $attrClass, string $attrStyle = ''): Radio
    {
        $this->thClass = $attrClass;
        $this->thStyle = $attrStyle;

        return $this;
    }

    public function label(string $attrClass, string $attrStyle = ''): Radio
    {
        $this->labelClass = $attrClass;
        $this->labelStyle = $attrStyle;

        return $this;
    }

    public function input(string $attrClass, string $attrStyle = ''): Radio
    {
        $this->inputClass = $attrClass;
        $this->inputStyle = $attrStyle;

        return $this;
    }

    public function div(string $attrClass): Radio
    {
        $this->divClass = $attrClass;

        return $this;
    }
}
