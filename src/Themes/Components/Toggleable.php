<?php

namespace PowerComponents\LivewirePowerGrid\Themes\Components;

class Toggleable
{
    public string $view = '';

    public function view(string $view): Toggleable
    {
        $this->view = $view;

        return $this;
    }
}
