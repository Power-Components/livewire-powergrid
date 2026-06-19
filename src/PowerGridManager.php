<?php

namespace PowerComponents\LivewirePowerGrid;

use PowerComponents\LivewirePowerGrid\Components\SetUp\{Cache,
    Detail,
    Exportable,
    Footer,
    Header,
    Responsive
};
use PowerComponents\LivewirePowerGrid\Plugins\Editable\EditablePlugin;
use PowerComponents\LivewirePowerGrid\Plugins\Flatpickr\FlatpickrPlugin;
use PowerComponents\LivewirePowerGrid\Plugins\Toggleable\ToggleablePlugin;

class PowerGridManager
{
    /** @var list<class-string> */
    public static array $plugins = [
        EditablePlugin::class,
        FlatpickrPlugin::class,
        ToggleablePlugin::class,
    ];

    /** @param  list<class-string>  $plugins */
    public static function plugins(array $plugins): void
    {
        static::$plugins = $plugins;
    }

    public function fields(): PowerGridFields
    {
        return app(PowerGridFields::class);
    }

    public function header(): Header
    {
        return app(Header::class);
    }

    public function footer(): Footer
    {
        return app(Footer::class);
    }

    public function detail(): Detail
    {
        return app(Detail::class);
    }

    public function responsive(): Responsive
    {
        return app(Responsive::class);
    }

    public function cache(): Cache
    {
        return app(Cache::class);
    }

    public function exportable(string $fileName = 'export'): Exportable
    {
        return app(Exportable::class, [
            'fileName' => $fileName,
        ]);
    }
}
