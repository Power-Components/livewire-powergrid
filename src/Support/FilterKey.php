<?php

namespace PowerComponents\LivewirePowerGrid\Support;

final class FilterKey
{
    private const DOT = '__pgdot__';

    public static function encode(string $field): string
    {
        return str_replace('.', self::DOT, $field);
    }

    public static function decode(string $key): string
    {
        return str_replace(self::DOT, '.', $key);
    }

    /**
     * Deferred panel binding: wire:model plus a stable data attribute so
     * Apply can read values even when Livewire drops nested keys on a JS [].
     *
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
     * Encode the field-level keys of a full draftFilters structure
     * (type => [field => value]).
     *
     * @param  array<string, mixed>  $draft
     * @return array<string, array<string, mixed>>
     */
    public static function encodeDraft(array $draft): array
    {
        return self::mapFieldKeys($draft, [self::class, 'encode']);
    }

    /**
     * Decode the field-level keys of a full draftFilters structure.
     *
     * @param  array<string, mixed>  $draft
     * @return array<string, array<string, mixed>>
     */
    public static function decodeDraft(array $draft): array
    {
        return self::mapFieldKeys($draft, [self::class, 'decode']);
    }

    /**
     * Rename the field-level keys of a single filter type array
     * (e.g. the "multi_select" sub-array).
     *
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
