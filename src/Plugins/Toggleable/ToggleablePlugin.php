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

    public static function ruleModifiers(): array
    {
        return ['toggleableVisibility', 'fieldHideToggleable'];
    }

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

    public function handles(Column|array $column): bool
    {
        return ! empty(data_get($column, 'pluginData.toggleable'));
    }

    public function render(Column|array $column, mixed $row): ?string
    {
        $showToggleable = $this->shouldShowToggleable($column, $row);

        return view('powergrid-plugins::Toggleable.index', [
            'tableName' => $this->component->tableName,
            'primaryKey' => $this->component->realPrimaryKey,
            'row' => $row,
            'column' => $column,
            'showToggleable' => $showToggleable,
            'js' => file_get_contents(__DIR__.'/index.js'),
        ])->render();
    }

    #[On('pg:toggleable-{tableName}')]
    public function toggleableChanged(mixed ...$params): void
    {
        [$field, $id, $value] = $params;

        $this->component->onUpdatedToggleable($id, $field, $value);
    }

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

        if ($fieldHideToggleable || str_contains(strval(data_get($column, 'field')), '.')) {
            return false;
        }

        return $showToggleable;
    }
}
