<?php

namespace PowerComponents\LivewirePowerGrid\Themes\Components;

class Toggleable
{
    public string $base = '';

    public function base(string $base): Toggleable
    {
        $this->base    = $base;

        return $this;
    }
}
