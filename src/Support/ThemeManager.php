<?php

namespace PowerComponents\LivewirePowerGrid\Support;

use PowerComponents\LivewirePowerGrid\Themes\{Tailwind, Theme};

class ThemeManager
{
    /** @var array<string, string> */
    protected static array $tokenCache = [];

    public static function current(): ?Theme
    {
        $theme = app()->bound('powergrid.theme') ? app('powergrid.theme') : null;

        return $theme instanceof Theme ? $theme : null;
    }

    public static function theme(string $key, string $default = ''): string
    {
        $theme = static::current();

        if ($theme === null) {
            return $default;
        }

        // Key the cache by theme identity so multiple themes in one request don't leak tokens.
        $cacheKey = $theme::class.'::'.$key;

        if (isset(static::$tokenCache[$cacheKey])) {
            return static::$tokenCache[$cacheKey];
        }

        /** @var string $value */
        $value = data_get($theme->resolveTokens(), $key, $default);
        static::$tokenCache[$cacheKey] = $value;

        return $value;
    }

    public static function view(string $alias): string
    {
        if ($alias === 'table.thead') {
            return 'livewire-powergrid::components.partials.thead';
        }

        if ($alias === 'table.tbody') {
            return 'livewire-powergrid::components.partials.tbody';
        }

        return (static::current() ?? new Tailwind())->resolveView($alias);
    }

    public static function clearCache(): void
    {
        static::$tokenCache = [];
    }
}
