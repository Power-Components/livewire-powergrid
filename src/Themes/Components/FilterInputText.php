<?php

namespace PowerComponents\LivewirePowerGrid\Themes\Components;

class FilterInputText
{
    public string $inputClass = '';

    public string $divClassNotInline = '';

    public string $divClassInline = '';

    public string $selectClass = '';

    public function selectClass(string $attrClass): FilterInputText
    {
        $this->selectClass = $attrClass;

        return $this;
    }

    public function inputClass(string $attrClass): FilterInputText
    {
        $this->inputClass = $attrClass;

        return $this;
    }

    public function divClassNotInline(string $attrClass): FilterInputText
    {
        $this->divClassNotInline = $attrClass;

        return $this;
    }

    public function divClassInline(string $attrClass): FilterInputText
    {
        $this->divClassInline = $attrClass;

        return $this;
    }
}
