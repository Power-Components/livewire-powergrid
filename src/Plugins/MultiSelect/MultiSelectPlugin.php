<?php

namespace PowerComponents\LivewirePowerGrid\Plugins\MultiSelect;

use PowerComponents\LivewirePowerGrid\Plugins\PluginBase;

class MultiSelectPlugin extends PluginBase
{
    public function name(): string
    {
        return 'multi_select';
    }

    public function isEnabled(): bool
    {
        return collect($this->component->declaredFilters())
            ->contains(fn ($filter) => str_contains($filter::class, 'FilterMultiSelect'));
    }

    public function onFilterCleared(string $field, string $type): void
    {
        if ($type === 'multi_select') {
            $this->component->dispatch('pg:clear_multi_select::'.$this->component->tableName.':'.$field);
        }
    }

    public function onFiltersCleared(): void
    {
        $this->component->dispatch('pg:clear_all_multi_select::'.$this->component->tableName);
    }

    public function onFiltersRestored(): void
    {
        $this->component->dispatch('pg:restore_multi_select::'.$this->component->tableName);
    }
}
