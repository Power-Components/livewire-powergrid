<?php

namespace PowerComponents\LivewirePowerGrid;

use PowerComponents\LivewirePowerGrid\Themes\{Bootstrap5, Tailwind, ThemeBase};
use Illuminate\Support\Collection;

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

    public static function theme(string $class): object
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
