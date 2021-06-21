<?php

namespace PowerComponents\LivewirePowerGrid\Themes\Components;

class PerPage
{
    public string $selectClass = '';

    public string $selectStyle = '';

    public string $view = '';

    public function select(string $attrClass='', string $attrStyle=''): PerPage
    {
        $this->selectClass    = $attrClass;
        $this->selectStyle    = $attrStyle;

        return $this;
    }

    public function view(string $path): PerPage
    {
        $this->view = $path;

        return $this;
    }
}
