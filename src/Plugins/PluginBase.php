<?php

namespace PowerComponents\LivewirePowerGrid\Plugins;

use PowerComponents\LivewirePowerGrid\{Column, PowerGridComponent};
use PowerComponents\Turbine\Column as TurbineColumn;
use stdClass;

/**
 * @phpstan-consistent-constructor
 */
abstract class PluginBase
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
     * @return list<string>
     */
    public static function ruleModifiers(): array
    {
        return [];
    }

    /**
     * Process rule data for the row. Plugins can extract their own
     * visibility/modifier data from the formatted rules array.
     * Returns key-value pairs to merge into the row's __turbine_rules entry.
     *
     * @param  array<string, mixed>  $rule
     * @return array<string, mixed>
     */
    public function processRuleModifiers(array $rule, bool $apply): array
    {
        return [];
    }

    abstract public function name(): string;

    abstract public function isEnabled(): bool;

    /** @param  TurbineColumn|array<mixed>|stdClass  $column */
    public function handles(TurbineColumn|array|stdClass $column): bool
    {
        return false;
    }

    protected function isHandledField(mixed $field): bool
    {
        if (! is_string($field) || $field === '') {
            return false;
        }

        return $this->handledColumn($field) !== null;
    }

    /**
     * Whether the server-declared column for the given field is handled by
     * this plugin. Unlike handles(), this inspects columns() — the declared
     * definition — instead of the client-hydrated snapshot.
     */
    public function isDeclaredField(string $field): bool
    {
        return $this->handledColumn($field) !== null;
    }

    /** @return Column|array<mixed, mixed>|stdClass|null */
    protected function handledColumn(string $field): mixed
    {
        foreach ($this->component->declaredColumns() as $column) {
            if ($column instanceof Column || $column instanceof stdClass || is_array($column)) {
                if ($this->handles($column) && data_get($column, 'dataField', data_get($column, 'field')) === $field) {
                    return $column;
                }
            }
        }

        return null;
    }

    /** @param  TurbineColumn|array<string, mixed>|stdClass  $column */
    public function render(TurbineColumn|array|stdClass $column, mixed $row): ?string
    {
        return null;
    }

    public function renderAssets(): string
    {
        return '';
    }

    /**
     * Whether this plugin renders content into the given UI zone
     * (e.g. 'header', 'toolbar', 'footer'). Feature plugins that are not
     * bound to a column override this to inject their own UI.
     */
    public function handlesZone(string $zone): bool
    {
        return false;
    }

    /**
     * Render this plugin's content for the given UI zone. Return null to
     * render nothing. The plugin reads any needed state from $this->component.
     */
    public function renderZone(string $zone): ?string
    {
        return null;
    }

    /**
     * Return theme token overrides for this plugin.
     * Merged into the theme's resolved tokens.
     *
     * @return array<string, mixed>
     */
    public static function themeTokens(): array
    {
        return [];
    }
}
