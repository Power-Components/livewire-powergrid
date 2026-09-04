<?php

namespace PowerComponents\LivewirePowerGrid;

use PowerComponents\LivewirePowerGrid\Components\SetUp\{Exportable, Tabs};
use PowerComponents\LivewirePowerGrid\Plugins\Editable\EditablePlugin;
use PowerComponents\LivewirePowerGrid\Plugins\Export\ExportPlugin;
use PowerComponents\LivewirePowerGrid\Plugins\FilterBuilder\FilterBuilderPlugin;
use PowerComponents\LivewirePowerGrid\Plugins\Flatpickr\FlatpickrPlugin;
use PowerComponents\LivewirePowerGrid\Plugins\PluginBase;
use PowerComponents\LivewirePowerGrid\Plugins\Tabs\TabsPlugin;
use PowerComponents\LivewirePowerGrid\Plugins\Toggleable\ToggleablePlugin;
use PowerComponents\LivewirePowerGrid\Plugins\Truncate\TruncatePlugin;
use PowerComponents\LivewirePowerGrid\Themes\{DaisyUI, Flux, Tailwind, Theme};
use PowerComponents\Turbine\Components\SetUp\{Cache, Detail, FilterBuilder, Footer, Header, Responsive};

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
        TabsPlugin::class,
        ToggleablePlugin::class,
        TruncatePlugin::class,
    ];

    /** @var list<class-string<PluginBase>> */
    public static array $plugins = self::DEFAULT_PLUGINS;

    /**
     * Built-in themes, selectable by name in config (`'theme' => 'daisyui'`).
     *
     * @var array<string, class-string<Theme>>
     */
    public const array DEFAULT_THEMES = [
        'tailwind' => Tailwind::class,
        'daisyui' => DaisyUI::class,
        'flux' => Flux::class,
    ];

    /** @var array<string, class-string<Theme>> */
    public static array $themes = self::DEFAULT_THEMES;

    /**
     * Register a theme under a name so apps (and satellite packages) can select
     * it via `config('livewire-powergrid.theme')` without referencing the FQCN.
     *
     * @param  class-string<Theme>  $class
     */
    public static function registerTheme(string $name, string $class): void
    {
        static::$themes[$name] = $class;
    }

    /**
     * Resolve a configured theme (registered name or FQCN) to its class-string.
     */
    public static function resolveThemeClass(string $nameOrClass): string
    {
        return static::$themes[$nameOrClass] ?? $nameOrClass;
    }

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

    public function tabs(): Tabs
    {
        return app(Tabs::class);
    }
}
