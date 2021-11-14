<?php

namespace PowerComponents\LivewirePowerGrid;

use Illuminate\Support\Collection;
use PowerComponents\LivewirePowerGrid\Themes\{Bootstrap5, Tailwind, ThemeBase};

class PowerGridManager
{
    /** @param Collection|null $collection */
    public function eloquent($collection = null): PowerGridEloquent
    {
        return new PowerGridEloquent($collection);
    }

    public function collection(): PowerGridCollection
    {
        return new PowerGridCollection();
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
