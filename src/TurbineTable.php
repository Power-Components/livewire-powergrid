<?php

namespace PowerComponents\LivewirePowerGrid;

use LogicException;
use PowerComponents\Turbine\Contracts\GridSchema;

abstract class TurbineTable extends PowerGridComponent
{
    public function datasource(): mixed
    {
        $definition = $this->definition();

        if ($definition === null) {
            throw new LogicException(static::class.' must implement definition() returning a '.GridSchema::class.'.');
        }

        return $definition->datasource();
    }
}
