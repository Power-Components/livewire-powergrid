<?php

namespace PowerComponents\LivewirePowerGrid\DataSource\Builders;

use Livewire\Component;
use PowerComponents\LivewirePowerGrid\Components\Filters\FilterBase;

class BuilderBase
{
    public static function make(Component $component, FilterBase $filterBase): self
    {
        return new self($component, $filterBase);
    }

    /**
     * @param  null|array<string, mixed>|FilterBase  $filterBase
     */
    public function __construct(
        protected Component $component,
        protected null|array|FilterBase $filterBase = null
    ) {}

    /**
     * @param  array<int|string, mixed>  $value
     * @return array{0: string, 1: mixed}
     */
    protected static function appendNestedField(string $field, array $value): array
    {
        $key = key($value);

        if (is_string($key) && preg_match('/^[A-Za-z0-9_.>-]+$/', $key) === 1) {
            $field = $field.'.'.$key;
        }

        return [$field, $value[$key]];
    }
}
