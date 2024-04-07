<?php

use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\Themes\{Bootstrap5, Tailwind, ThemeBase};

if (!function_exists('powerGridThemeRoot')) {
    /**
     * @throws Exception
     */
    function powerGridThemeRoot(): string
    {
        /** @var ThemeBase $theme */
        $theme = PowerGrid::theme(strval(config('livewire-powergrid.theme')));

        return $theme->root();
    }
}

if (!function_exists('powerGridTheme')) {
    /**
     * @throws Exception
     */
    function powerGridTheme(): string
    {
        return strval(config('livewire-powergrid.theme'));
    }
}

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

if (!function_exists('convertObjectsToArray')) {
    function convertObjectsToArray(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_object($value)) {
                $data[$key] = (array) $value;
            } elseif (is_array($value)) {
                $data[$key] = convertObjectsToArray($value);
            }
        }

        return $data;
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
