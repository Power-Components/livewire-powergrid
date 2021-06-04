<?php

namespace PowerComponents\LivewirePowerGrid\Themes\Components;

class Editable
{
    public string $spanClass = '';

    public string $view = '';

    public function view(string $view): Editable
    {
        $this->view    = $view;

        return $this;
    }
    public function spanClass(string $spanClass): Editable
    {
        $this->spanClass    = $spanClass;

        return $this;
    }
}
