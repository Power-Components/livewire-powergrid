<?php

namespace PowerComponents\LivewirePowerGrid\Traits;

use Illuminate\Support\Collection;
use PowerComponents\LivewirePowerGrid\Column;

trait WithSorting
{
    public string $sortField = 'id';

    public string $sortDirection = 'asc';

    public bool $withSortStringNumber = false;

    public bool $multiSort = false;

    public array $sortArray = [];

    public function sortBy(string $field, string $direction = 'asc'): void
    {
        if ($this->multiSort) {
            $this->sortByArray($field);

            return;
        }

        $this->sortDirection = $this->sortField === $field ? $this->reverseSort() : $direction;

        $this->sortField = $field;
    }

    public function reverseSort(): string
    {
        return $this->sortDirection === 'asc' ? 'desc' : 'asc';
    }

    public function applySorting(Collection $query): Collection
    {
        if ($this->multiSort) {
            return $this->applySortingArray($query);
        }

        if (is_a($query, Collection::class)) {
            return $query->sortBy($this->sortField, SORT_REGULAR, !(($this->sortDirection === 'asc')));
        }

        return $query->orderBy($this->sortField, $this->sortDirection);
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

    public function applySortingArray(Collection $query): Collection
    {
        if (is_a($query, Collection::class)) {
            $formattedSortingArray = [];

            foreach ($this->sortArray as $sortField => $sortDirection) {
                $formattedSortingArray[] = [$sortField, $sortDirection];
            }

            return $query->sortBy($formattedSortingArray);
        }

        foreach ($this->sortArray as $sortField => $sortDirection) {
            $query = $query->orderBy($sortField, $sortDirection);
        }

        return $query;
    }

    public function getLabelFromColumn(string $field): string
    {
        $filter = collect($this->columns)->filter(
            fn (Column|\stdClass $column) => $column->dataField == $field
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
}
