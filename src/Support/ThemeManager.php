<?php

namespace PowerComponents\LivewirePowerGrid\Support;

use PowerComponents\LivewirePowerGrid\Themes\{Tailwind, Theme};

class ThemeManager
{
    /** @var array<string, string> */
    protected static array $tokenCache = [];

    public static function theme(string $key, string $default = ''): string
    {
        /** @var Theme|null $theme */
        $theme = app()->bound('powergrid.theme') ? app('powergrid.theme') : null;

        if (! $theme instanceof Theme) {
            return $default;
        }

        // Key the cache by theme identity so multiple themes in one request don't leak tokens.
        $cacheKey = $theme::class.'::'.$key;

        if (isset(static::$tokenCache[$cacheKey])) {
            return static::$tokenCache[$cacheKey];
        }

        $value = strval(data_get($theme->resolveTokens(), $key, $default));
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

        /** @var Theme|null $theme */
        $theme = app()->bound('powergrid.theme') ? app('powergrid.theme') : null;

        if (! $theme instanceof Theme) {
            return (new Tailwind())->resolveView($alias);
        }

        return $theme->resolveView($alias);
    }

    public static function clearCache(): void
    {
        static::$tokenCache = [];
    }
}
