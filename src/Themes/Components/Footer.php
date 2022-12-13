<?php

namespace PowerComponents\LivewirePowerGrid\Themes\Components;

class Footer
{
    public string $selectClass = '';

    public string $selectStyle = '';

    public string $view = '';

    public function select(string $attrClass = '', string $attrStyle = ''): Footer
    {
        $this->selectClass = $attrClass;
        $this->selectStyle = $attrStyle;

        return $this;
    }

    public function view(string $path): Footer
    {
        $this->view = $path;

        return $this;
    }
}
