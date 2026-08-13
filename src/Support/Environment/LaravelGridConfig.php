<?php

namespace PowerComponents\LivewirePowerGrid\Support\Environment;

use PowerComponents\LivewirePowerGrid\Contracts\GridConfig;

final class LaravelGridConfig implements GridConfig
{
    public function get(string $key, mixed $default = null): mixed
    {
        return config($key, $default);
    }
}
