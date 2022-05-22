<?php

namespace PowerComponents\LivewirePowerGrid;

use PowerComponents\LivewirePowerGrid\Themes as Themes;

class PowerGridManager
{
    public function eloquent(): PowerGridEloquent
    {
        return new PowerGridEloquent();
    }

    /**
     * @param string $class
     * @return object|Themes\ThemeBase
     */
    public static function theme(string $class)
    {
        if ($class === 'tailwind') {
            $class = Themes\Tailwind::class;
        }
        if ($class === 'bootstrap') {
            $class = Themes\Bootstrap5::class;
        }

        return new $class();
    }
}
