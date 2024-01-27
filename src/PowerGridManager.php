<?php

namespace PowerComponents\LivewirePowerGrid;

use PowerComponents\LivewirePowerGrid\Themes as Themes;

class PowerGridManager
{
    /** @deprecated until 6.x */
    public function columns(): PowerGridColumns
    {
        return new PowerGridColumns();
    }

    public function fields(): PowerGridFields
    {
        return new PowerGridFields();
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
