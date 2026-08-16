<?php

namespace PowerComponents\LivewirePowerGrid;

use PowerComponents\LivewirePowerGrid\Plugins\Editable\EditablePlugin;
use PowerComponents\LivewirePowerGrid\Plugins\Export\ExportPlugin;
use PowerComponents\LivewirePowerGrid\Plugins\FilterBuilder\FilterBuilderPlugin;
use PowerComponents\LivewirePowerGrid\Plugins\Flatpickr\FlatpickrPlugin;
use PowerComponents\LivewirePowerGrid\Plugins\PluginBase;
use PowerComponents\LivewirePowerGrid\Plugins\Toggleable\ToggleablePlugin;
use PowerComponents\Turbine\Components\SetUp\{Cache, Detail, Exportable, FilterBuilder, Footer, Header, Responsive};

class PowerGridManager
{
    /**
     * Built-in plugins shipped with Turbine. Always registered so custom
     * plugin registration can never drop them.
     *
     * @var list<class-string<PluginBase>>
     */
    public const array DEFAULT_PLUGINS = [
        EditablePlugin::class,
        ExportPlugin::class,
        FilterBuilderPlugin::class,
        FlatpickrPlugin::class,
        ToggleablePlugin::class,
    ];

    /** @var list<class-string<PluginBase>> */
    public static array $plugins = self::DEFAULT_PLUGINS;

    /**
     * Register additional plugins. The built-in plugins are always kept;
     * the given list is merged on top (de-duplicated), so customizing plugins
     * never removes a built-in (e.g. Export).
     *
     * @param  list<class-string<PluginBase>>  $plugins
     */
    public static function plugins(array $plugins): void
    {
        /** @var list<class-string<PluginBase>> $merged */
        $merged = array_values(array_unique([...self::DEFAULT_PLUGINS, ...$plugins]));

        static::$plugins = $merged;
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

    public function filterBuilder(): FilterBuilder
    {
        return app(FilterBuilder::class);
    }
}
