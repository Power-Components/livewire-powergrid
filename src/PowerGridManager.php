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

    public static function theme(string $class = Tailwind::class): ThemeBase
    {
        if ($class === 'bootstrap') {
            $class = Bootstrap5::class;
        }

        return new $class();
    }
}
