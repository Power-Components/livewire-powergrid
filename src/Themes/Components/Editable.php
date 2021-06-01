<?php

namespace PowerComponents\LivewirePowerGrid\Themes\Components;

class Editable
{
    public string $spanClass = '';

    public string $base = '';

    public function base(string $base): Editable
    {
        $this->base    = $base;

        return $this;
    }
    public function spanClass(string $spanClass): Editable
    {
        $this->spanClass    = $spanClass;

        return $this;
    }
}
