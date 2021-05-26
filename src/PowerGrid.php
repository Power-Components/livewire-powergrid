<?php

namespace PowerComponents\LivewirePowerGrid;

use Illuminate\Support\Facades\Facade;

/**
 * @method static PowerGridEloquent eloquent($collection=[])
 * @method static PowerGridEloquent collection()
 * createFromModel
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
