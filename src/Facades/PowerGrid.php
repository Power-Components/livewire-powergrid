<?php

namespace PowerComponents\LivewirePowerGrid\Facades;

use Illuminate\Support\Facades\Facade;
use PowerComponents\LivewirePowerGrid\Components\SetUp\{Exportable, Tabs};
use PowerComponents\LivewirePowerGrid\{PowerGridFields, PowerGridManager};
use PowerComponents\Turbine\Components\SetUp\{Cache, Detail, FilterBuilder, Footer, Header, Responsive};

/**
 * @method static PowerGridFields fields()
 * @method static Header header()
 * @method static Footer footer()
 * @method static Detail detail()
 * @method static Cache cache()
 * @method static Exportable exportable(string $fileName)
 * @method static FilterBuilder filterBuilder()
 * @method static Responsive responsive()
 * @method static Tabs tabs()
 * @method static void plugins(array<string, object> $plugins)
 */
class PowerGrid extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return PowerGridManager::class;
    }
}
