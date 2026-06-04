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

    /**
     * Boot the plugin (register macros, rule types, theme tokens, etc).
     * Called once at service provider level.
     */
    public static function boot(): void {}

    /**
     * Return the rule modifiers this plugin contributes to the rules engine.
     * Override in your plugin to register custom modifiers.
     *
     * @return array<string>
     */
    public static function ruleModifiers(): array
    {
        return [];
    }

    /**
     * Process rule data for the row. Plugins can extract their own
     * visibility/modifier data from the formatted rules array.
     * Returns key-value pairs to merge into the row's __powergrid_rules entry.
     */
    public function processRuleModifiers(array $rule, bool $apply): array
    {
        return [];
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

    /**
     * Return theme token overrides for this plugin.
     * Merged into the theme's resolved tokens.
     */
    public static function themeTokens(): array
    {
        return [];
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
