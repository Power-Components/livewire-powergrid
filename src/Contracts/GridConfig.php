<?php

namespace PowerComponents\LivewirePowerGrid\Contracts;

interface GridConfig
{
    public function get(string $key, mixed $default = null): mixed;
}
