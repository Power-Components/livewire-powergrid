<?php

namespace PowerComponents\LivewirePowerGrid\Themes\Components;

class Cols
{
    public string $divClass = '';

    public string $divStyle = '';

    public string $clearFilterClass = '';

    public string $clearFilterStyle = '';

    public function div(string $attrClass = '', string $attrStyle = ''): Cols
    {
        $this->divClass    = $attrClass;
        $this->divStyle    = $attrStyle;

        return $this;
    }

    public function clearFilter(string $attrClass = '', string $attrStyle = ''): Cols
    {
        $this->clearFilterClass    = $attrClass;
        $this->clearFilterStyle    = $attrStyle;

        return $this;
    }
}
