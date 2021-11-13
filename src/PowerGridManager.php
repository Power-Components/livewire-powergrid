<?php

namespace PowerComponents\LivewirePowerGrid;

use PowerComponents\LivewirePowerGrid\Themes\{Bootstrap5, Tailwind, ThemeBase};

class PowerGridManager
{
    public function eloquent($collection = null): PowerGridEloquent
    {
        return new PowerGridEloquent($collection);
    }

    public function collection(): PowerGridCollection
    {
        return new PowerGridCollection();
    }

    public static function theme(string $class): ThemeBase
    {
        if ($class === 'tailwind') {
            $class = \PowerComponents\LivewirePowerGrid\Themes\Tailwind::class;
        }
        if ($class === 'bootstrap') {
            $class = \PowerComponents\LivewirePowerGrid\Themes\Bootstrap5::class;
        }

        return new $class();
    }
}
