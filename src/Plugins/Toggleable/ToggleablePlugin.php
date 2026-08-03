<?php

namespace PowerComponents\LivewirePowerGrid\Plugins\Toggleable;

use Livewire\Attributes\On;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Components\Rules\{RuleManager, RuleToggleable};
use PowerComponents\LivewirePowerGrid\Plugins\PluginBase;
use stdClass;

class ToggleablePlugin extends PluginBase
{
    public static function boot(): void
    {
        Column::macro('toggleable', function (
            bool $hasPermission = true,
            string $trueLabel = 'Yes',
            string $falseLabel = 'No',
        ): Column {
            /** @var Column $this */
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
        return collect($this->component->columns)
            ->contains(fn ($column) => ! empty(data_get($column, 'pluginData.toggleable')));
    }

    /** @param  Column|array<string, mixed>|stdClass  $column */
    public function handles(Column|array|stdClass $column): bool
    {
        return ! empty(data_get($column, 'pluginData.toggleable'));
    }

    protected static ?string $cachedJs = null;

    /** @param  Column|array<string, mixed>|stdClass  $column */
    public function render(Column|array|stdClass $column, mixed $row): ?string
    {
        $showToggleable = $this->shouldShowToggleable($column, $row);

        static::$cachedJs ??= file_get_contents(__DIR__.'/index.js') ?: '';

        /** @var view-string $viewName */
        $viewName = 'powergrid-plugins::Toggleable.index';

        return view($viewName, [
            'tableName' => $this->component->tableName,
            'primaryKey' => $this->component->realPrimaryKey,
            'row' => $row,
            'column' => $column,
            'showToggleable' => $showToggleable,
            'js' => static::$cachedJs,
        ])->render();
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

        $this->component->onUpdatedToggleable((string) $id, $field, (string) $value);
    }

    /** @param  stdClass|Column|array<string, mixed>  $column */
    private function shouldShowToggleable(stdClass|Column|array $column, mixed $row): bool
    {
        $showToggleable = boolval(data_get($column, 'pluginData.toggleable.enabled', false));

        $toggleableRowRules = data_get(
            collect((array) data_get($row, '__powergrid_rules'))
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
            collect((array) data_get($row, '__powergrid_rules'))
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
