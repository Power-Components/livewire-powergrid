<?php

namespace PowerComponents\LivewirePowerGrid\Plugins;

use Livewire\Wireable;
use PowerComponents\LivewirePowerGrid\{Column, PowerGridComponent};

/**
 * @phpstan-consistent-constructor
 */
abstract class PluginBase implements Wireable
{
    public function __construct(
        protected PowerGridComponent $component
    ) {}

    public static function make(PowerGridComponent $component): static
    {
        return new static($component);
    }

    abstract public function name(): string;

    abstract public function isEnabled(): bool;

    public function handles(Column|array $column): bool
    {
        return false;
    }

    public function render(Column|array $column, mixed $row): ?string
    {
        return null;
    }

    public function toLivewire(): array
    {
        return [];
    }

    public static function fromLivewire($value): static
    {
        return new static(app(PowerGridComponent::class));
    }
}
