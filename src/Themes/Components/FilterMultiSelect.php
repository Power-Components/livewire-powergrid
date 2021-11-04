<?php

namespace PowerComponents\LivewirePowerGrid\Themes\Components;

class FilterMultiSelect
{
    public string $inputClass = '';

    public string $divClassNotInline = '';

    public string $divClassInline = '';

    public string $view = '';

    public function input(string $attrClass = ''): FilterMultiSelect
    {
        $this->inputClass = $attrClass;

        return $this;
    }

    public function divNotInline(string $attrClass = ''): FilterMultiSelect
    {
        $this->divClassNotInline = $attrClass;

        return $this;
    }

    public function divInline(string $attrClass = ''): FilterMultiSelect
    {
        $this->divClassInline = $attrClass;

        return $this;
    }

    public function view(string $view): FilterMultiSelect
    {
        $this->view    = $view;

        return $this;
    }
}
