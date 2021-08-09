<?php

namespace PowerComponents\LivewirePowerGrid;

use Illuminate\Support\Facades\Facade;
use PowerComponents\LivewirePowerGrid\Themes\ThemeBase;

/**
 * @method static PowerGridEloquent eloquent($collection=[])
 * @method static PowerGridCollection collection()
 * @method static ThemeBase theme(string $class)
 *
 * @see \PowerComponents\LivewirePowerGrid\PowerGridManager
 */
class PowerGrid extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'powergrid';
    }
}
