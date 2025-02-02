<?php

namespace PowerComponents\LivewirePowerGrid\Concerns;

use PowerComponents\LivewirePowerGrid\Column;
use stdClass;

trait ManageRow
{
    public function prepareRowTemplates(): void
    {
        $rowTemplates = json_encode($this->rowTemplates());

        $this->js(<<<JS
            this[`pgRowTemplates_\${\$wire.id}`] = $rowTemplates
        JS);
    }

    public function shouldShowEditOnClick(stdClass|Column|array $column, mixed $row): bool
    {
        $hasPermission = boolval(data_get($column, 'editable.hasPermission', false));

        $editOnClickVisibility = data_get(
            collect((array) $row->__powergrid_rules) // @phpstan-ignore-line
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

    public function shouldShowToggleable(stdClass|Column|array $column, mixed $row): bool
    {
        $showToggleable = boolval(data_get($column, 'toggleable.enabled', false));

        $toggleableRowRules = data_get(
            collect((array) $row->__powergrid_rules) // @phpstan-ignore-line
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
            collect((array) $row->__powergrid_rules) // @phpstan-ignore-line
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
