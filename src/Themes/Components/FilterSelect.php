<?php

namespace PowerComponents\LivewirePowerGrid\Themes\Components;

class FilterSelect
{
    public string $inputClass = '';

    public string $divClassNotInline = '';

    public string $divClassInline = '';

    public function inputClass(string $inputClass): FilterSelect
    {
        $this->inputClass = $inputClass;

        return $this;
    }

    public function divClassNotInline(string $divClassNotInline): FilterSelect
    {
        $this->divClassNotInline = $divClassNotInline;

        return $this;
    }

    public function divClassInline(string $divClassInline): FilterSelect
    {
        $this->divClassInline = $divClassInline;

        return $this;
    }
}
