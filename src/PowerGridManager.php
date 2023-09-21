<?php

namespace PowerComponents\LivewirePowerGrid;

use PowerComponents\LivewirePowerGrid\Themes as Themes;

class PowerGridManager
{
    public function columns(): PowerGridColumns
    {
        return new PowerGridColumns();
    }

    /**
     * @param string $class
     * @return object|Themes\ThemeBase
     */
    public static function theme(string $class)
    {
        if ($class === 'tailwind') {
            $class = Themes\Tailwind::class;
        }

        if ($class === 'bootstrap') {
            $class = Themes\Bootstrap5::class;
        }

        return new $class();
    }
}
