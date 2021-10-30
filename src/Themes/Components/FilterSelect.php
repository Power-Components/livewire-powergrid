<?php

namespace PowerComponents\LivewirePowerGrid\Themes\Components;

class FilterSelect
{
    public string $inputClass = '';

    public string $divClassNotInline = '';

    public string $divClassInline = '';

    public string $relativeDivClass = '';

    public function input(string $attrClass = ''): FilterSelect
    {
        $this->inputClass = $attrClass;

        return $this;
    }

    public function divNotInline(string $attrClass = ''): FilterSelect
    {
        $this->divClassNotInline = $attrClass;

        return $this;
    }

    public function divInline(string $attrClass = ''): FilterSelect
    {
        $this->divClassInline = $attrClass;

        return $this;
    }

    public function relativeDiv(string $attrClass = ''): FilterSelect
    {
        $this->relativeDivClass = $attrClass;

        return $this;
    }
}
