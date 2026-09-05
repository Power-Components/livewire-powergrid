<?php

use PowerComponents\LivewirePowerGrid\Support\ThemeManager;

if (! function_exists('powergrid_components_path')) {
    function powergrid_components_path(string $filename = ''): string
    {
        /** @var string $namespace */
        $namespace = config('livewire.class_namespace');

        return base_path(
            str($namespace)
                ->replace('App', 'app')
                ->append(DIRECTORY_SEPARATOR.$filename)
                ->replace('\\', '/')
                ->replace('//', '/')
                ->replace('/', DIRECTORY_SEPARATOR)
                ->toString()
        );
    }
}

if (! function_exists('powergrid_stubs_path')) {
    function powergrid_stubs_path(string $filename = ''): string
    {
        return str(__DIR__.'/../resources/stubs/')
            ->append($filename)
            ->replace('/', DIRECTORY_SEPARATOR)
            ->rtrim(DIRECTORY_SEPARATOR)
            ->toString();
    }
}

if (! function_exists('theme')) {
    function theme(string $key, string $default = ''): string
    {
        return ThemeManager::theme($key, $default);
    }
}

if (! function_exists('theme_view')) {
    function theme_view(string $alias): string
    {
        return ThemeManager::view($alias);
    }
}
