<?php

namespace PowerComponents\LivewirePowerGrid\FilterAttributes;

use PowerComponents\LivewirePowerGrid\Support\FilterKey;

final class FilterWireAttributes
{
    /** @var array<string, class-string> */
    private const array DEFAULTS = [
        'boolean' => Boolean::class,
        'input_text' => InputText::class,
        'number' => Number::class,
        'select' => Select::class,
    ];

    /**
     * @param  array<string, mixed>  $filter
     * @return array<string, mixed>
     */
    public static function forView(array $filter, bool $deferred, string $title = ''): array
    {
        $filter = FilterKey::withModelKey($filter);
        $filter['deferred'] = $deferred;
        $filter['filtersProperty'] = $deferred ? 'draftFilters' : 'filters';

        $type = $filter['key'] ?? null;

        if (! is_string($type) || self::classFor($type) === null) {
            return $filter;
        }

        $arg = $type === 'number'
            ? array_merge($filter, ['title' => $title])
            : $title;

        return array_merge($filter, self::get($type, $filter, $arg, $deferred));
    }

    public static function modelKey(mixed $filter): string
    {
        return is_string($filter) ? $filter : FilterKey::fromFilter($filter);
    }

    /**
     * @param  mixed  $filter  Filter definition (array/object) or a resolved model key (string).
     * @param  string|array<string, mixed>  $arg
     * @param  bool  $deferred  When true, bind to `draftFilters.*` (panel Apply).
     *                          When false, bind `filters.*` with `wire:model.live`.
     * @return array<string, mixed>
     */
    public static function get(string $key, mixed $filter, string|array $arg, bool $deferred = false): array
    {
        $class = self::classFor($key) ?? throw new \InvalidArgumentException("Unknown filter attribute type [{$key}].");

        /** @var callable-object $instance */
        $instance = new $class();

        return $instance(self::modelKey($filter), $arg, $deferred);
    }

    /** @return class-string|null */
    private static function classFor(string $key): ?string
    {
        $class = config('livewire-powergrid.filter_attributes.'.$key, self::DEFAULTS[$key] ?? null);

        return is_string($class) && class_exists($class) ? $class : null;
    }
}
