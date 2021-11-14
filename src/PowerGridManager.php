<?php

namespace PowerComponents\LivewirePowerGrid;

use PowerComponents\LivewirePowerGrid\Themes\{Bootstrap5, Tailwind, ThemeBase};

class PowerGridManager
{
    public function eloquent(): PowerGridEloquent
    {
        return new PowerGridEloquent();
    }

    /**
     * @param string $class
     * @return object|ThemeBase
     */
    public static function theme(string $class)
    {
        if ($class === 'tailwind') {
            $class = Tailwind::class;
        }
        if ($class === 'bootstrap') {
            $class = Bootstrap5::class;
        }

        return new $class();
    }
}
