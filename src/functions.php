<?php

use PowerComponents\LivewirePowerGrid\Themes\{Bootstrap5, Tailwind};

if (!function_exists('isBootstrap5')) {
    function isBootstrap5(): bool
    {
        return in_array(config('livewire-powergrid.theme'), ['bootstrap', Bootstrap5::class]);
    }
}

if (!function_exists('isTailwind')) {
    function isTailwind(): bool
    {
        return in_array(config('livewire-powergrid.theme'), ['tailwind', Tailwind::class]);
    }
}

if (!function_exists('powergrid_components_path')) {
    function powergrid_components_path(string $filename = ''): string
    {
        return base_path(
            str(strval(config('livewire.class_namespace')))
                ->replace('App', 'app')
                ->append(DIRECTORY_SEPARATOR . $filename)
                ->replace('\\', '/')
                ->replace('//', '/')
                ->replace('/', DIRECTORY_SEPARATOR)
                ->toString()
        );
    }
}

if (!function_exists('powergrid_stubs_path')) {
    function powergrid_stubs_path(string $filename = ''): string
    {
        return str(__DIR__ . '/../resources/stubs/')
            ->append($filename)
            ->replace('/', DIRECTORY_SEPARATOR)
            ->rtrim(DIRECTORY_SEPARATOR)
            ->toString();
    }
}

if (!function_exists('once')) {
    function once(callable $callback): mixed
    {
        return $callback();
    }
}

if (!function_exists('theme_style')) {
    function theme_style(array $theme, string $name, ?string $default = null): string
    {
        return strval(
            data_get($theme, str($name)->append('.0')) ?? data_get($theme, $name, $default)
        );
    }
}
