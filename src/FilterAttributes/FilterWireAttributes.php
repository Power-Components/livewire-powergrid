<?php

namespace PowerComponents\LivewirePowerGrid\FilterAttributes;

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
     * @param  string|array<string, mixed>  $arg
     * @param  bool  $deferred  When true, bind to `draftFilters.*` with a deferred
     *                          `wire:model` and emit no live handler (panel modes).
     * @return array<string, mixed>
     */
    public static function get(string $key, string $field, string|array $arg, bool $deferred = false): array
    {
        /** @var class-string $class */
        $class = config('livewire-powergrid.filter_attributes.'.$key, self::DEFAULTS[$key] ?? null);

        /** @var callable-object $instance */
        $instance = new $class();

        return $instance($field, $arg, $deferred);
    }
}
