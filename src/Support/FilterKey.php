<?php

namespace PowerComponents\LivewirePowerGrid\Support;

use PowerComponents\Turbine\Support\FilterBag;

final class FilterKey
{
    private const DOT = '__pgdot__';

    public static function encode(string $field): string
    {
        return str_replace('.', self::DOT, $field);
    }

    public static function modelKey(string $column, ?string $field = null): string
    {
        $key = FilterBag::bagKey($column, $field);

        return str_contains($key, '.') ? self::encode($key) : $key;
    }

    public static function decode(string $key): string
    {
        return str_replace(self::DOT, '.', $key);
    }

    /**
     * @return array<string, string>
     */
    public static function liveModel(string $path, bool $debounce = true): array
    {
        $attribute = $debounce
            ? 'wire:model.live.debounce.600ms'
            : 'wire:model.live';

        return [$attribute => 'filters.'.$path];
    }

    /**
     * @return array{'wire:model': string, 'data-pg-draft': string}
     */
    public static function draftModel(string $path): array
    {
        return [
            'wire:model' => 'draftFilters.'.$path,
            'data-pg-draft' => $path,
        ];
    }

    /**
     * Encode top-level field keys of a field-keyed filter bag.
     *
     * @param  array<string, mixed>  $draft
     * @return array<string, mixed>
     */
    public static function encodeDraft(array $draft): array
    {
        $out = [];

        foreach ($draft as $field => $record) {
            $out[self::encode((string) $field)] = $record;
        }

        return $out;
    }

    /**
     * Decode top-level field keys. Legacy type-keyed bags decode nested field keys.
     *
     * @param  array<string, mixed>  $draft
     * @return array<string, mixed>
     */
    public static function decodeDraft(array $draft): array
    {
        if (FilterBag::isLegacy($draft)) {
            return self::mapFieldKeys($draft, [self::class, 'decode']);
        }

        $out = [];

        foreach ($draft as $field => $record) {
            $out[self::decode((string) $field)] = $record;
        }

        return $out;
    }

    /**
     * @param  array<string, mixed>  $type
     * @return array<string, mixed>
     */
    public static function decodeType(array $type): array
    {
        $out = [];

        foreach ($type as $field => $value) {
            $out[(string) (is_string($field) ? self::decode($field) : $field)] = $value;
        }

        return $out;
    }

    /**
     * @param  array<string, mixed>  $draft
     * @param  callable(string): string  $fn
     * @return array<string, array<string, mixed>>
     */
    private static function mapFieldKeys(array $draft, callable $fn): array
    {
        $out = [];

        foreach ($draft as $type => $fields) {
            $renamed = [];

            foreach ((array) $fields as $field => $value) {
                $renamed[(string) (is_string($field) ? $fn($field) : $field)] = $value;
            }

            $out[$type] = $renamed;
        }

        return $out;
    }
}
