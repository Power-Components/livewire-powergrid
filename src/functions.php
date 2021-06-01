<?php

const BOOTSTRAP            = 'bootstrap';
const TAILWIND             = 'tailwind';
const JS_FRAMEWORK_ALPINE  = 'alpinejs';

if (!function_exists('powerGridTheme')) {
    function powerGridTheme(): string
    {
        return config('livewire-powergrid.theme');
    }
}
if (!function_exists('powerGridJsFramework')) {
    function powerGridJsFramework(): string
    {
        return config('livewire-powergrid.js_framework');
    }
}
if (!function_exists('powerGridCache')) {
    function powerGridCache(): bool
    {
        return config('livewire-powergrid.cached_data');
    }
}

