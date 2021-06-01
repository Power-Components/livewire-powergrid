<?php

namespace PowerComponents\LivewirePowerGrid\Themes\Components;

class FilterBoolean
{
    public string $inputClass = '';

    public string $divClassNotInline = '';

    public string $divClassInline = '';

    public string $divStyle = '';

    public function inputClass(string $inputClass): FilterBoolean
    {
        $this->inputClass = $inputClass;

        return $this;
    }

    public function divClassNotInline(string $divClassNotInline): FilterBoolean
    {
        $this->divClassNotInline = $divClassNotInline;

        return $this;
    }

    public function divClassInline(string $divClassInline): FilterBoolean
    {
        $this->divClassInline = $divClassInline;

        return $this;
    }

    public function divStyle(string $divStyle): FilterBoolean
    {
        $this->divStyle = $divStyle;

        return $this;
    }
}
