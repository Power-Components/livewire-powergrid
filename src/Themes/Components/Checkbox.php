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

    public function thClass(string $attrClass, string $attrStyle=''): Checkbox
    {
        $this->thClass    = $attrClass;
        $this->thStyle    = $attrStyle;

        return $this;
    }

    public function labelClass(string $attrClass, string $attrStyle=''): Checkbox
    {
        $this->labelClass = $attrClass;
        $this->labelStyle = $attrStyle;

        return $this;
    }

    public function inputClass (string $attrClass, string $attrStyle=''): Checkbox
    {
        $this->inputClass = $attrClass;
        $this->inputStyle = $attrStyle;

        return $this;
    }
}
