<?php

namespace PowerComponents\LivewirePowerGrid\Concerns\State;

use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Contracts\PowerGridContext;

/** @phpstan-require-implements PowerGridContext */
trait ResolvesGridSorting
{
    public function resolveSortField(string $sortField): string
    {
        if (str_contains($sortField, '.') || $this->state()->ignoreTablePrefix) {
            return $sortField;
        }

        return $this->getCurrentTable().'.'.$sortField;
    }

    public function isValidSortField(string $sortField): bool
    {
        return collect($this->declaredColumns())
            ->map(fn ($column) => data_get($column, 'dataField') ?: data_get($column, 'field'))
            ->filter()
            ->contains($sortField);
    }

    public function getSortCallback(string $field): ?\Closure
    {
        $columns = $this->declaredColumns();

        foreach ($columns as $column) {
            $columnDataField = data_get($column, 'dataField');

            if ($columnDataField === $field && data_get($column, 'sortCallback') instanceof \Closure) {
                return $column instanceof Column ? $column->sortCallback : null;
            }
        }

        return null;
    }
}
