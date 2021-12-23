<?php

namespace PowerComponents\LivewirePowerGrid\Themes\Components;

class Row
{
    public string $spanClass = '';

    public string $spanStyle = '';

    public function span(string $attrClass = '', string $attrStyle = ''): Row
    {
        $this->spanClass    = $attrClass;
        $this->spanStyle    = $attrStyle;

        return $this;
    }
}
