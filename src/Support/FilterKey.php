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

    public static function fromFilter(mixed $filter): string
    {
        $stamped = data_get($filter, 'modelKey');

        if (is_string($stamped) && $stamped !== '') {
            return $stamped;
        }

        $field = data_get($filter, 'field');
        $column = data_get($filter, 'column');

        if (! is_string($column) || $column === '') {
            $column = is_string($field) ? $field : '';
        }

        return self::modelKey($column, is_string($field) ? $field : null);
    }

    /**
     * @param  array<string, mixed>  $filter
     * @return array<string, mixed>
     */
    public static function withModelKey(array $filter): array
    {
        $filter['modelKey'] = self::fromFilter($filter);

        return $filter;
    }

    public static function decode(string $key): string
    {
        return str_replace(self::DOT, '.', $key);
    }

    /**
     * Bind the live bag with the given Livewire modifiers ('live.debounce.600ms',
     * 'live', 'blur', … or '' for a plain `wire:model`).
     *
     * @return array<string, string>
     */
    public static function liveModel(string $path, string $modifiers = 'live'): array
    {
        $attribute = 'wire:model'.($modifiers === '' ? '' : '.'.trim($modifiers, '.'));

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
     * Encode the top-level field keys of a field-keyed filter bag.
     *
     * @param  array<string, mixed>  $draft
     * @return array<string, mixed>
     */
    public static function encodeDraft(array $draft): array
    {
        return self::mapKeys($draft, self::encode(...));
    }

    /**
     * @param  array<string, mixed>  $draft
     * @return array<string, mixed>
     */
    public static function decodeDraft(array $draft): array
    {
        return self::mapKeys($draft, self::decode(...));
    }

    /**
     * @param  array<string, mixed>  $bag
     * @param  callable(string): string  $fn
     * @return array<string, mixed>
     */
    private static function mapKeys(array $bag, callable $fn): array
    {
        $out = [];

        foreach ($bag as $field => $record) {
            $out[$fn((string) $field)] = $record;
        }

        return $out;
    }
}
