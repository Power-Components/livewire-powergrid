<?php

namespace PowerComponents\LivewirePowerGrid;

use Illuminate\Support\Facades\Facade;

/**
 * @mixin \PowerComponents\LivewirePowerGrid\PowerGridManager
 */
class PowerGrid extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return 'powergrid';
    }
}
