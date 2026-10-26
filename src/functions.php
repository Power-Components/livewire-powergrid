<?php

use Illuminate\Support\Js;
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

if (! function_exists('js_literal')) {
    function js_literal(mixed $value): string
    {
        $scalar = is_string($value) || is_int($value) || is_float($value) || is_bool($value) || $value === null
            ? $value
            : ($value instanceof Stringable
                ? (string) $value
                : throw new InvalidArgumentException('js_literal() expects a scalar or Stringable value.'));

        if (is_int($scalar) || is_float($scalar) || is_bool($scalar) || $scalar === null) {
            $scalar = (string) $scalar;
        }

        return e((string) Js::from($scalar));
    }
}

if (! function_exists('js_embed')) {
    function js_embed(mixed $value): string
    {
        $literal = (string) Js::from(is_scalar($value) || $value === null ? (string) $value : '');

        return substr($literal, 1, -1);
    }
}
