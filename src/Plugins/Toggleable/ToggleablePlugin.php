<?php

namespace PowerComponents\LivewirePowerGrid\Plugins\Toggleable;

use Livewire\Attributes\On;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Plugins\PluginBase;
use PowerComponents\Turbine\Column as TurbineColumn;
use PowerComponents\Turbine\Components\Rules\{RuleManager, RuleToggleable};
use stdClass;

class ToggleablePlugin extends PluginBase
{
    public static function boot(): void
    {
        Column::macro('toggleable', function (
            bool $hasPermission = true,
            string $trueLabel = 'Yes',
            string $falseLabel = 'No',
        ): TurbineColumn {
            /** @var TurbineColumn $this */
            $this->pluginData['editable'] = [];
            $this->pluginData['toggleable'] = [
                'enabled' => $hasPermission,
                'default' => [$trueLabel, $falseLabel],
            ];

            return $this;
        });

        RuleManager::registerModifiers(static::ruleModifiers());

        RuleManager::macro('toggleable', function (string $column): RuleToggleable {
            return new RuleToggleable($column);
        });
    }

    /** @return list<string> */
    public static function ruleModifiers(): array
    {
        return ['toggleableVisibility', 'fieldHideToggleable'];
    }

    /**
     * @param  array<string, mixed>  $rule
     * @return array{toggleableVisibility: mixed, fieldHideToggleable: bool}
     */
    public function processRuleModifiers(array $rule, bool $apply): array
    {
        return [
            'toggleableVisibility' => $apply ? data_get($rule, 'rule.toggleableVisibility') : [],
            'fieldHideToggleable' => $apply && (bool) data_get($rule, 'rule.fieldHideToggleable'),
        ];
    }

    public function name(): string
    {
        return 'toggleable';
    }

    public function isEnabled(): bool
    {
        return collect($this->component->declaredColumns())
            ->contains(fn ($column) => ! empty(data_get($column, 'pluginData.toggleable')));
    }

    /** @param  TurbineColumn|array<string, mixed>|stdClass  $column */
    public function handles(TurbineColumn|array|stdClass $column): bool
    {
        return ! empty(data_get($column, 'pluginData.toggleable'));
    }

    protected static ?string $cachedJs = null;

    protected static ?string $cachedCss = null;

    /**
     * Global assets (<script>/<style>) emitted once per table by the root layout.
     * Rendered as a plain string — no Blade view, no per-row compile.
     */
    public function renderAssets(): string
    {
        static::$cachedJs ??= file_get_contents(__DIR__.'/index.js') ?: '';
        static::$cachedCss ??= file_get_contents(__DIR__.'/index.css') ?: '';

        return '<script>'.static::$cachedJs.'</script>'
            .'<style>'.static::$cachedCss.'</style>';
    }

    /**
     * Build the per-row switch cell as a PHP HTML string (replaces the old
     * per-row Blade view, which was the top cost in the tbody render path).
     *
     * @param  TurbineColumn|array<string, mixed>|stdClass  $column
     */
    public function render(TurbineColumn|array|stdClass $column, mixed $row): ?string
    {
        $showToggleable = $this->shouldShowToggleable($column, $row);

        $field = data_get($column, 'field');
        $field = is_string($field) ? $field : '';

        $rawValue = data_get($row, $field);
        $value = is_numeric($rawValue) ? (int) $rawValue : 0;

        $default = data_get($column, 'pluginData.toggleable.default');
        $default = is_array($default) ? $default : [];

        $trueValue = isset($default[0]) && is_scalar($default[0]) ? (string) $default[0] : 'Yes';
        $falseValue = isset($default[1]) && is_scalar($default[1]) ? (string) $default[1] : 'No';

        $html = '<div class="flex flex-row justify-center">';

        if ($showToggleable) {
            $params = json_encode([
                'id' => data_get($row, $this->component->realPrimaryKey),
                'isHidden' => false,
                'tableName' => $this->component->tableName,
                'field' => data_get($column, 'field'),
                'toggle' => $value,
                'trueValue' => $trueValue,
                'falseValue' => $falseValue,
            ]);

            $html .= '<div'
                .' x-data="pgToggleable"'
                .' data-pg-params="'.e((string) $params).'"'
                .' role="switch"'
                .' tabindex="0"'
                .' :aria-checked="ariaChecked()"'
                .' :class="onClass()"'
                .' class="pg-toggleable-switch relative inline-block w-8 h-4 rounded-full cursor-pointer transition-colors duration-200 ease-linear"'
                .' style="'.$this->switchVars().'"'
                .' x-on:click="save()"'
                .' x-on:keydown.enter.prevent="save()"'
                .' x-on:keydown.space.prevent="save()"'
                .'>'
                .'<span class="pg-toggleable-knob absolute left-0 top-0 block w-4 h-4 rounded-full"></span>'
                .'</div>';
        } else {
            $badgeClass = $value === 0 ? 'bg-red-200 text-red-800' : 'bg-blue-200 text-blue-800';

            $html .= '<div class="text-xs px-4 w-auto py-1 text-center rounded-md '.$badgeClass.'">'
                .e($value === 0 ? $falseValue : $trueValue)
                .'</div>';
        }

        return $html.'</div>';
    }

    private function switchVars(): string
    {
        $colorOn = theme('toggleable.color_on', 'var(--color-accent, #16a34a)');
        $colorOff = theme('toggleable.color_off', 'var(--color-zinc-200, #e4e4e7)');
        $colorOnDark = theme('toggleable.color_on_dark', $colorOn);
        $colorOffDark = theme('toggleable.color_off_dark', $colorOff);
        $knobOn = theme('toggleable.knob_on', 'var(--color-accent-foreground, #ffffff)');

        return "--pg-toggle-on-light: {$colorOn};"
            ." --pg-toggle-off-light: {$colorOff};"
            ." --pg-toggle-on-dark: {$colorOnDark};"
            ." --pg-toggle-off-dark: {$colorOffDark};"
            ." --pg-toggle-knob-on: {$knobOn};";
    }

    #[On('pg:toggleable-{tableName}')]
    public function toggleableChanged(mixed ...$params): void
    {
        $field = $params[0] ?? null;
        $id = $params[1] ?? null;
        $value = $params[2] ?? null;

        if (! is_string($field) || ! is_scalar($id) || ! is_scalar($value) || ! $this->isHandledField($field)) {
            return;
        }

        $column = $this->handledColumn($field);
        if ($column === null || ! boolval(data_get($column, 'pluginData.toggleable.enabled'))) {
            return;
        }

        $this->component->onUpdatedToggleable((string) $id, $field, (string) $value);
    }

    /** @param  stdClass|TurbineColumn|array<string, mixed>  $column */
    private function shouldShowToggleable(stdClass|TurbineColumn|array $column, mixed $row): bool
    {
        $showToggleable = boolval(data_get($column, 'pluginData.toggleable.enabled', false));

        $toggleableRowRules = data_get(
            collect((array) data_get($row, '__turbine_rules'))
                ->where('apply', true)
                ->last(),
            'toggleableVisibility'
        );

        if ($toggleableRowRules === 'hide') {
            return false;
        }

        if ($toggleableRowRules === 'show') {
            return true;
        }

        $fieldHideToggleable = (bool) data_get(
            collect((array) data_get($row, '__turbine_rules'))
                ->where('apply', true)
                ->last(),
            'fieldHideToggleable'
        );

        $field = data_get($column, 'field');
        if ($fieldHideToggleable || (is_string($field) && str_contains($field, '.'))) {
            return false;
        }

        return $showToggleable;
    }
}
