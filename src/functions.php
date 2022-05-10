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

if (!function_exists('validateInputTextOptions')) {
    function validateInputTextOptions(array $filter, string $field): bool
    {
        return data_get($filter, "input_text_options.$field") !== null && in_array(
            strtolower(strval(data_get($filter, "input_text_options.$field"))),
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
