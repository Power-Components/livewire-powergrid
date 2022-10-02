<?php

namespace PowerComponents\LivewirePowerGrid\Themes\Components;

class SearchBox
{
    public string $inputClass = '';

    public string $inputStyle = '';

    public string $iconSearchClass = '';

    public string $iconSearchStyle = '';

    public string $iconCloseClass = '';

    public string $iconCloseStyle = '';

    public function input(string $attrClass, string $attrStyle = ''): SearchBox
    {
        $this->inputClass = $attrClass;

        $this->inputStyle = $attrStyle;

        return $this;
    }

    public function iconSearch(string $attrClass, string $attrStyle = ''): SearchBox
    {
        $this->iconSearchClass = $attrClass;

        $this->iconSearchStyle = $attrStyle;

        return $this;
    }

    public function iconClose(string $attrClass, string $attrStyle = ''): SearchBox
    {
        $this->iconCloseClass = $attrClass;

        $this->iconCloseStyle = $attrStyle;

        return $this;
    }
}
