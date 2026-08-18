<?php

namespace PowerComponents\LivewirePowerGrid\Support\Synth;

use Closure;
use Livewire\Mechanisms\HandleComponents\Synthesizers\Synth;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\Turbine\Contracts\Definition;
use ReflectionClass;

class PowerGridWireableSynth extends Synth
{
    /** @var string */
    public static $key = 'pgwrbl';

    /** @var array<class-string, list<string>> */
    private const TRANSIENT = [
        Column::class => ['filters', 'rawQueries', 'sortCallback', 'summaryCallbacks'],
    ];

    /** @param  mixed  $target */
    public static function match($target): bool
    {
        return $target instanceof Definition;
    }

    /**
     * @param  Definition  $target
     * @return array<array-key, mixed>
     */
    public static function unwrapForValidation($target)
    {
        return self::toArray($target);
    }

    /**
     * @param  Definition  $target
     * @param  callable(int|string, mixed): mixed  $dehydrateChild
     * @return array{0: array<array-key, mixed>, 1: array{class: class-string}}
     */
    public function dehydrate($target, $dehydrateChild): array
    {
        $data = self::toArray($target);

        foreach ($data as $key => $child) {
            $data[$key] = $dehydrateChild($key, $child);
        }

        return [
            $data,
            ['class' => get_class($target)],
        ];
    }

    /**
     * @param  array<array-key, mixed>  $value
     * @param  array{class?: class-string}  $meta
     * @param  callable(int|string, mixed): mixed  $hydrateChild
     * @return mixed
     */
    public function hydrate($value, $meta, $hydrateChild)
    {
        if (! isset($meta['class']) || ! is_a($meta['class'], Definition::class, true)) {
            throw new \Exception('PowerGrid: Invalid definition class.');
        }

        foreach ($value as $key => $child) {
            $value[$key] = $hydrateChild($key, $child);
        }

        /** @var object $instance */
        $instance = (new ReflectionClass($meta['class']))->newInstanceWithoutConstructor();

        foreach ($value as $key => $val) {
            if (property_exists($instance, (string) $key)) {
                $instance->{$key} = $val;
            }
        }

        return $instance;
    }

    /**
     * @param  object  $target
     * @param  int|string  $key
     * @param  mixed  $value
     */
    public function set(&$target, $key, $value): void
    {
        $target->{$key} = $value;
    }

    /** @return array<array-key, mixed> */
    private static function toArray(object $target): array
    {
        $data = get_object_vars($target);

        foreach (self::TRANSIENT[get_class($target)] ?? [] as $field) {
            unset($data[$field]);
        }

        return array_filter($data, static fn ($value): bool => ! $value instanceof Closure);
    }
}
