<?php

namespace PowerComponents\LivewirePowerGrid\Themes\Components;

class FilterNumber
{
    public string $inputClass = '';

    public string $divClassNotInline = '';

    public string $divClassInline = '';

    public function inputClass(string $inputClass): FilterNumber
    {
        $this->inputClass = $inputClass;

        return $this;
    }

    public function divClassNotInline(string $divClassNotInline): FilterNumber
    {
        $this->divClassNotInline = $divClassNotInline;

        return $this;
    }

    public function divClassInline(string $divClassInline): FilterNumber
    {
        $this->divClassInline = $divClassInline;

        return $this;
    }
}
