<?php

namespace PowerComponents\LivewirePowerGrid\Themes\Components;

class FilterNumber
{
    public string $inputClass = '';

    public string $divClassNotInline = '';

    public string $divClassInline = '';

    public function input(string $attrClass=''): FilterNumber
    {
        $this->inputClass = $attrClass;

        return $this;
    }

    public function divNotInline(string $attrClass=''): FilterNumber
    {
        $this->divClassNotInline = $attrClass;

        return $this;
    }

    public function divInline(string $attrClass=''): FilterNumber
    {
        $this->divClassInline = $attrClass;

        return $this;
    }
}
