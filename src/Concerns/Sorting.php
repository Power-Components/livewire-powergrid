<?php

namespace PowerComponents\LivewirePowerGrid\Concerns;

use Exception;
use PowerComponents\LivewirePowerGrid\Column;
use stdClass;

trait Sorting
{
    public string $sortField = 'id';

    public string $sortDirection = 'asc';

    public bool $multiSort = false;

    public array $sortArray = [];

    /**
     * @throws Exception
     */
    public function sortBy(string $field, string $direction = 'asc'): void
    {
        if ($this->multiSort) {
            $this->sortByArray($field);

            $this->afterSort();

            return;
        }

        $this->sortDirection = $this->sortField === $field ? $this->reverseSort() : $direction;

        $this->sortField = $field;

        $this->persistState('sorting');

        $this->afterSort();
    }

    public function afterSort(): void
    {
        if (isset($this->setUp['detail'])) {
            return;
        }

        if (function_exists('partials')) {
            partials($this)
                ->partial("pg-thead-{$this->tableName}", theme_view('table.thead'))
                ->partial("pg-tbody-{$this->tableName}", theme_view('table.tbody'))
                ->partial("pg-pagination-{$this->tableName}", theme_view('footer'));
        }
    }

    public function reverseSort(): string
    {
        return $this->sortDirection === 'asc' ? 'desc' : 'asc';
    }

    public function sortByArray(string $field): void
    {
        if (array_key_exists($field, $this->sortArray)) {
            $prevDirection = $this->sortArray[$field];

            if ($prevDirection == 'asc') {
                $this->sortArray[$field] = 'desc';
            } elseif ($prevDirection == 'desc') {
                unset($this->sortArray[$field]);
            }

            return;
        }

        $this->sortArray[$field] = 'asc';
    }

    public function getLabelFromColumn(string $field): string
    {
        $filter = collect($this->columns)->filter(
            fn (Column|stdClass|array $column) => data_get($column, 'dataField') == $field
        )->map(fn ($column) => (array) $column)
            ->first();

        return strval(data_get($filter, 'title'));
    }

    public function sortLabel(string $field): void
    {
        if ($this->multiSort && array_key_exists($field, $this->sortArray)) {
            echo ($this->sortArray[$field] == 'desc') ? '&#8595;' : '&#8593;';
        } elseif ($this->multiSort) {
            echo '&#8597;';
        } else {
            if ($this->sortField !== $field) {
                echo '&#8597;';
            } elseif ($this->sortDirection == 'desc') {
                echo '&#8593;';
            } else {
                echo '&#8595;';
            }
        }
    }

    public function showSortIcon(string $field): string
    {
        if ($this->multiSort && array_key_exists($field, $this->sortArray)) {
            return ($this->sortArray[$field] == 'desc') ? 'livewire-powergrid::components.icons.chevron-up' : 'livewire-powergrid::components.icons.chevron-down';
        } elseif ($this->multiSort) {
            return 'livewire-powergrid::components.icons.chevron-up-down';
        } else {
            if ($this->sortField !== $field) {
                return 'livewire-powergrid::components.icons.chevron-up-down';
            } elseif ($this->sortDirection == 'desc') {
                return 'livewire-powergrid::components.icons.chevron-up';
            } else {
                return 'livewire-powergrid::components.icons.chevron-down';
            }
        }
    }

    /**
     * Get the sort callback for a given field from the columns definition.
     * Returns null if no custom callback is defined.
     */
    public function getSortCallback(string $field): ?\Closure
    {
        $columns = $this->columns();

        foreach ($columns as $column) {
            $columnDataField = data_get($column, 'dataField');

            if ($columnDataField === $field && data_get($column, 'sortCallback') instanceof \Closure) {
                return $column->sortCallback;
            }
        }

        return null;
    }
}
