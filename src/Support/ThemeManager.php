<?php

namespace PowerComponents\LivewirePowerGrid\Support;

use PowerComponents\LivewirePowerGrid\Themes\{Tailwind, Theme};

class ThemeManager
{
    public static function theme(string $key, string $default = ''): string
    {
        /** @var Theme|null $theme */
        $theme = app()->bound('powergrid.theme') ? app('powergrid.theme') : null;

        if (! $theme instanceof Theme) {
            return $default;
        }

        return strval(data_get($theme->resolveTokens(), $key, $default));
    }

    public static function view(string $alias): string
    {
        /** @var Theme|null $theme */
        $theme = app()->bound('powergrid.theme') ? app('powergrid.theme') : null;

        if (! $theme instanceof Theme) {
            return (new Tailwind())->resolveView($alias);
        }

        return $theme->resolveView($alias);
    }
}
