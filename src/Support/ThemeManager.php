<?php

namespace PowerComponents\LivewirePowerGrid\Support;

use PowerComponents\LivewirePowerGrid\Themes\{Tailwind, Theme};

class ThemeManager
{
    protected static array $tokenCache = [];

    public static function theme(string $key, string $default = ''): string
    {
        if (isset(static::$tokenCache[$key])) {
            return static::$tokenCache[$key];
        }

        /** @var Theme|null $theme */
        $theme = app()->bound('powergrid.theme') ? app('powergrid.theme') : null;

        if (! $theme instanceof Theme) {
            return $default;
        }

        $value = strval(data_get($theme->resolveTokens(), $key, $default));
        static::$tokenCache[$key] = $value;

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
