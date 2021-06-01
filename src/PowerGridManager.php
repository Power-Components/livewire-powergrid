<?php

namespace PowerComponents\LivewirePowerGrid;

use PowerComponents\LivewirePowerGrid\Themes\ThemeBase;

class PowerGridManager
{
    public function eloquent($collection=null): PowerGridEloquent
    {
        return new PowerGridEloquent($collection);
    }

    public function collection(): PowerGridCollection
    {
        return new PowerGridCollection();
    }

    public static function theme(string $class): ThemeBase
    {
        return new $class;
    }
}
