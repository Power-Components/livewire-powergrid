<?php

namespace PowerComponents\LivewirePowerGrid\Plugins\Toggleable;

use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Plugins\PluginBase;
use stdClass;

class ToggleablePlugin extends PluginBase
{
    public function name(): string
    {
        return 'toggleable';
    }

    public function isEnabled(): bool
    {
        return collect($this->component->columns)
            ->contains(fn ($column) => ! empty(data_get($column, 'toggleable')));
    }

    public function handles(Column|array $column): bool
    {
        return ! empty(data_get($column, 'toggleable'));
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

    private function shouldShowToggleable(stdClass|Column|array $column, mixed $row): bool
    {
        $showToggleable = boolval(data_get($column, 'toggleable.enabled', false));

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
