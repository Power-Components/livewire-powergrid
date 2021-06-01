<?php

namespace PowerComponents\LivewirePowerGrid\Themes\Components;

class PerPage
{
    public string $selectClass = '';

    public string $selectStyle = '';

    public string $base = '';

    public function selectClass(string $attrClass, string $attrStyle=''): PerPage
    {
        $this->selectClass    = $attrClass;
        $this->selectStyle    = $attrStyle;

        return $this;
    }

    public function base(string $path): PerPage
    {
        $this->base = $path;

        return $this;
    }
}
