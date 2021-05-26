<?php

namespace PowerComponents\LivewirePowerGrid;

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

}
