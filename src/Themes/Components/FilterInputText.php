<?php

namespace PowerComponents\LivewirePowerGrid\Themes\Components;

class FilterInputText
{
    public string $inputClass = '';

    public string $divClassNotInline = '';

    public string $divClassInline = '';

    public string $selectClass = '';

    public string $relativeDivClass = '';

    public function select(string $attrClass, string $relativeDivClass = ''): FilterInputText
    {
        $this->selectClass = $attrClass;

        $this->relativeDivClass = $relativeDivClass;

        return $this;
    }

    public function input(string $attrClass): FilterInputText
    {
        $this->inputClass = $attrClass;

        return $this;
    }

    public function divNotInline(string $attrClass): FilterInputText
    {
        $this->divClassNotInline = $attrClass;

        return $this;
    }

    public function divInline(string $attrClass): FilterInputText
    {
        $this->divClassInline = $attrClass;

        return $this;
    }
}
