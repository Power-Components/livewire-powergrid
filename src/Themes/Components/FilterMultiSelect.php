<?php

namespace PowerComponents\LivewirePowerGrid\Themes\Components;

class FilterMultiSelect
{
    public string $inputClass = '';

    public string $divClassNotInline = '';

    public string $divClassInline = '';

    public function inputClass(string $inputClass): FilterMultiSelect
    {
        $this->inputClass = $inputClass;

        return $this;
    }

    public function divClassNotInline(string $divClassNotInline): FilterMultiSelect
    {
        $this->divClassNotInline = $divClassNotInline;

        return $this;
    }

    public function divClassInline(string $divClassInline): FilterMultiSelect
    {
        $this->divClassInline = $divClassInline;

        return $this;
    }
}
