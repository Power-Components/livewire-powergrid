<?php

namespace PowerComponents\LivewirePowerGrid\Plugins\Editable;

use Livewire\Attributes\On;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Plugins\PluginBase;
use PowerComponents\Turbine\Column as TurbineColumn;
use PowerComponents\Turbine\Components\Rules\{RuleEditOnClick, RuleManager};
use stdClass;

class EditablePlugin extends PluginBase
{
    public static function boot(): void
    {
        Column::macro('editOnClick', function (
            bool $hasPermission = true,
            string $dataField = '',
            ?string $fallback = null,
            bool $saveOnMouseOut = false,
        ): Column {
            /** @var Column $this */
            $this->pluginData['editable'] = [
                'hasPermission' => $hasPermission,
                'fallback' => $fallback,
                'saveOnMouseOut' => $saveOnMouseOut,
            ];

            if (filled($dataField)) {
                $this->dataField = $dataField;
            }

            return $this;
        });

        // Register rule modifiers
        RuleManager::registerModifiers(static::ruleModifiers());

        // Register rule factory method on RuleManager
        RuleManager::macro('editOnClick', function (string $column): RuleEditOnClick {
            return new RuleEditOnClick($column);
        });
    }

    /** @return list<string> */
    public static function ruleModifiers(): array
    {
        return ['editOnClickVisibility', 'fieldHideEditOnClick'];
    }

    /**
     * @param  array<string, mixed>  $rule
     * @return array{editOnClickVisibility: mixed, fieldHideEditOnClick: bool}
     */
    public function processRuleModifiers(array $rule, bool $apply): array
    {
        return [
            'editOnClickVisibility' => $apply ? data_get($rule, 'rule.editOnClickVisibility') : [],
            'fieldHideEditOnClick' => $apply && (bool) data_get($rule, 'rule.fieldHideEditOnClick'),
        ];
    }

    public function name(): string
    {
        return 'editable';
    }

    public function isEnabled(): bool
    {
        return collect($this->component->declaredColumns())
            ->contains(fn ($column) => ! empty(data_get($column, 'pluginData.editable')));
    }

    /** @param  TurbineColumn|array<string, mixed>|stdClass  $column */
    public function handles(TurbineColumn|array|stdClass $column): bool
    {
        return ! empty(data_get($column, 'pluginData.editable'));
    }

    protected static ?string $cachedJs = null;

    protected static ?string $cachedCss = null;

    /** @param  TurbineColumn|array<string, mixed>|stdClass  $column */
    public function render(TurbineColumn|array|stdClass $column, mixed $row): ?string
    {
        if ($this->shouldShowEditOnClick($column, $row)) {
            static::$cachedJs ??= file_get_contents(__DIR__.'/index.js') ?: '';
            static::$cachedCss ??= file_get_contents(__DIR__.'/index.css') ?: '';

            /** @var view-string $viewName */
            $viewName = 'powergrid-plugins::Editable.index';

            return view($viewName, [
                'tableName' => $this->component->tableName,
                'primaryKey' => $this->component->realPrimaryKey,
                'row' => $row,
                'field' => data_get($column, 'dataField', data_get($column, 'field')),
                'currentTable' => $this->component->currentTable,
                'showErrorBag' => config('livewire-powergrid.show_error_bag'),
                'editable' => data_get($column, 'pluginData.editable'),
                'js' => static::$cachedJs,
                'css' => static::$cachedCss,
            ])->render();
        }

        return null;
    }

    #[On('pg:editable-{tableName}')]
    public function inputTextChanged(mixed ...$params): void
    {
        [$field, $id, $value] = $params;
        /** @var string $value */
        if (! is_string($field) || ! is_scalar($id) || ! $this->isHandledField($field)) {
            return;
        }

        $column = $this->handledColumn($field);
        if ($column === null || ! boolval(data_get($column, 'pluginData.editable.hasPermission'))) {
            return;
        }

        $id = (string) $id;

        $component = $this->component;
        data_set($component, "$field.{$id}", $value);

        $this->component->onUpdatedEditable($id, $field, $value);

        $this->component->dispatch('pg:editable-close-'.$id);
    }

    /** @param  stdClass|TurbineColumn|array<string, mixed>  $column */
    private function shouldShowEditOnClick(stdClass|TurbineColumn|array $column, mixed $row): bool
    {
        $hasPermission = boolval(data_get($column, 'pluginData.editable.hasPermission', false));

        $editOnClickVisibility = data_get(
            collect((array) data_get($row, '__powergrid_rules'))
                ->where('apply', true)
                ->last(),
            'editOnClickVisibility'
        );

        if ($editOnClickVisibility === 'hide') {
            return false;
        }

        if ($editOnClickVisibility === 'show') {
            return true;
        }

        return $hasPermission;
    }
}
