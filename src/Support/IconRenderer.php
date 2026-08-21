<?php

namespace PowerComponents\LivewirePowerGrid\Support;

use Illuminate\Support\Facades\Blade;

/**
 * Compiles an icon reference into HTML.
 *
 * The reference is a blade component name (`funnel`, `icons.funnel`) or a
 * namespaced component (`livewire-powergrid::icons.filter`). Simple names are
 * statically folded into `<x-name />` for speed, everything else falls back to
 * `<x-dynamic-component />`.
 */
final class IconRenderer
{
    /** @var array<string, string> */
    private static array $cache = [];

    /** @param  array<string, mixed>  $attributes */
    public static function render(string $icon, array $attributes = []): string
    {
        if ($icon === '') {
            return '';
        }

        $cacheKey = $icon.'::'.md5(serialize($attributes));

        if (! isset(self::$cache[$cacheKey])) {
            try {
                self::$cache[$cacheKey] = self::compile($icon, $attributes);
            } catch (\Throwable) {
                self::$cache[$cacheKey] = '';
            }
        }

        return self::$cache[$cacheKey];
    }

    public static function flush(): void
    {
        self::$cache = [];
    }

    /** @param  array<string, mixed>  $attributes */
    private static function compile(string $icon, array $attributes): string
    {
        if (self::isStaticallyFoldable($icon, $attributes)) {
            try {
                return Blade::render(self::buildStaticTag($icon, $attributes));
            } catch (\Throwable) {
            }
        }

        return Blade::render(
            '<x-dynamic-component :component="$component" :attributes="new \Illuminate\View\ComponentAttributeBag($attrs)" />',
            ['component' => $icon, 'attrs' => $attributes],
        );
    }

    /** @param  array<string, mixed>  $attributes */
    private static function isStaticallyFoldable(string $icon, array $attributes): bool
    {
        if (preg_match('/^[A-Za-z0-9][A-Za-z0-9._-]*$/', $icon) !== 1) {
            return false;
        }

        foreach ($attributes as $key => $value) {
            if (! is_string($key) || preg_match('/^[A-Za-z_:][A-Za-z0-9_:.\-]*$/', $key) !== 1) {
                return false;
            }

            if (! is_scalar($value)) {
                return false;
            }

            if (is_string($value) && (str_contains($value, '{{') || str_contains($value, '{!!'))) {
                return false;
            }
        }

        return true;
    }

    /** @param  array<string, mixed>  $attributes */
    private static function buildStaticTag(string $icon, array $attributes): string
    {
        $rendered = '';

        foreach ($attributes as $key => $value) {
            if (is_bool($value)) {
                if ($value) {
                    $rendered .= ' '.$key;
                }

                continue;
            }

            $rendered .= ' '.$key.'="'.e(is_scalar($value) ? (string) $value : '').'"';
        }

        return "<x-{$icon}{$rendered} />";
    }
}
