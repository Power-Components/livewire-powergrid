<?php

namespace PowerComponents\LivewirePowerGrid\Concerns\Filters;

use Exception;
use Livewire\Attributes\On;
use PowerComponents\LivewirePowerGrid\Support\FilterKey;

trait HandlesFilterInputs
{
    /**
     * Livewire wrote `$filters.*` (inline `wire:model.live`). Commit the bag.
     *
     * @throws Exception
     */
    protected function filtersUpdated(string $name): void
    {
        if (str_starts_with($name, 'filters.')) {
            // The wire path is `filters.{key}.value…`; the key itself may be
            // `__pgdot__`-encoded, so split first and decode after.
            $field = FilterKey::decode((string) str($name)->after('filters.')->before('.'));

            if ($field !== '') {
                $this->notifyFilterChanged($field);
            }
        }

        $this->commitFilters();
    }

    /**
     * @param  list<string>  $values
     *
     * @throws Exception
     */
    #[On('pg:multiSelect-{tableName}')]
    public function multiSelectChanged(
        string $field,
        string $label,
        array $values,
    ): void {
        $this->putFilterRecord($field, 'multi_select', $values, label: $label);
        $this->notifyFilterChanged($field);

        if (count($values) === 0) {
            $this->clearFilter($field);

            return;
        }

        $this->commitFilters();
    }

    protected function notifyFilterChanged(string $field): void
    {
        $record = $this->filterRecord($field) ?? [];
        $record['type'] ??= $this->filterSchema()['types'][$this->filterBagKey($field)] ?? '';

        $this->afterFilterChanged($field, $record);
    }
}
