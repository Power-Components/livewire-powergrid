<?php

use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\Themes\{Bootstrap5, Tailwind, ThemeBase};
use PowerComponents\LivewirePowerGrid\Traits\Filter;

if (!function_exists('powerGridThemeRoot')) {
    /**
     * @throws Exception
     */
    function powerGridThemeRoot(): string
    {
        $configTheme = config('livewire-powergrid.theme');

        if (!is_string($configTheme)) {
            throw new \Exception('Theme not found!');
        }
        /** @var ThemeBase $theme */
        $theme = PowerGrid::theme($configTheme);

        return $theme->root();
    }
}

if (!function_exists('powerGridTheme')) {
    /**
     * @throws Exception
     */
    function powerGridTheme(): string
    {
        $powerGridTheme = config('livewire-powergrid.theme');

        if (!is_string($powerGridTheme)) {
            throw new \Exception('PowerGrid theme not found!');
        }

        return $powerGridTheme;
    }
}

if (!function_exists('validateInputTextOptions')) {
    function validateInputTextOptions(array $filter, string $field): bool
    {
        return isset($filter['input_text_options'][$field]) && in_array(
            strtolower($filter['input_text_options'][$field]),
            Filter::getInputTextOptions()
        );
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
