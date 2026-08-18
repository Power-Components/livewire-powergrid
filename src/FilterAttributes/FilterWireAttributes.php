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
     * @return array<string, mixed>
     */
    public static function get(string $key, string $field, string|array $arg): array
    {
        /** @var class-string $class */
        $class = config('livewire-powergrid.filter_attributes.'.$key, self::DEFAULTS[$key] ?? null);

        /** @var callable-object $instance */
        $instance = new $class();

        return $instance($field, $arg);
    }
}
