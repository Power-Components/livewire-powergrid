<?php

use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\Themes\ThemeBase;

const BOOTSTRAP            = 'bootstrap';
const TAILWIND             = 'tailwind';
const JS_FRAMEWORK_ALPINE  = 'alpinejs';

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

if (!function_exists('powerGridJsFramework')) {
    /**
     * @throws Exception
     */
    function powerGridJsFramework(): ?string
    {
        /** @var string|null $powerGridJsFramework */
        $powerGridJsFramework = config('livewire-powergrid.js_framework');

        return $powerGridJsFramework;
    }
}
if (!function_exists('powerGridCache')) {
    function powerGridCache(): bool
    {
        return boolval(config('livewire-powergrid.cached_data', false));
    }
}
if (!function_exists('validateInputTextOptions')) {
    function validateInputTextOptions(array $filter, string $field): bool
    {
        return isset($filter['input_text_options'][$field]) && in_array(
            strtolower($filter['input_text_options'][$field]),
            ['is', 'is_not', 'contains', 'contains_not', 'starts_with', 'ends_with', 'is_empty', 'is_not_empty', 'is_null', 'is_not_null', 'is_blank', 'is_not_blank']
        );
    }
}
