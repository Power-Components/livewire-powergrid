<?php

namespace PowerComponents\LivewirePowerGrid\Facades;

use Illuminate\Support\Facades\Facade;
use PowerComponents\LivewirePowerGrid\Components\SetUp\{Cache, Detail, Exportable, Footer, Header, Lazy, Responsive};
use PowerComponents\LivewirePowerGrid\{PowerGridFields, PowerGridManager};

/**
 * @method static PowerGridFields fields()
 * @method static Header header()
 * @method static Footer footer()
 * @method static Lazy lazy()
 * @method static Detail detail()
 * @method static Cache cache()
 * @method static Exportable exportable(string $fileName)
 * @method static Responsive responsive()
 */
class PowerGrid extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return PowerGridManager::class;
    }
}
