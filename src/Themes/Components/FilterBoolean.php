<?php

namespace PowerComponents\LivewirePowerGrid\Themes\Components;

class FilterBoolean
{
    public string $inputClass = '';

    public string $divClassNotInline = '';

    public string $divClassInline = '';

    public string $divStyle = '';

    public string $relativeDivClass = '';

    public function input(string $attrClass, string $divStyle = ''): FilterBoolean
    {
        $this->inputClass = $attrClass;

        $this->divStyle = $divStyle;

        return $this;
    }

    public function relativeDiv(string $attrClass): FilterBoolean
    {
        $this->relativeDivClass = $attrClass;

        return $this;
    }

    public function divNotInline(string $divClassNotInline): FilterBoolean
    {
        $this->divClassNotInline = $divClassNotInline;

        return $this;
    }

    public function divInline(string $divClassInline): FilterBoolean
    {
        $this->divClassInline = $divClassInline;

        return $this;
    }
}
