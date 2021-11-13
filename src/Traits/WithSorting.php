<?php

namespace PowerComponents\LivewirePowerGrid\Traits;

use Illuminate\Support\Collection;

trait WithSorting
{
    public string $sortField = 'id';

    public string $sortDirection = 'asc';

    public bool $withSortStringNumber = false;

    public function sortBy(string $field)
    {
        $this->sortDirection = $this->sortField === $field
            ? $this->reverseSort()
            : 'asc';

        $this->sortField = $field;
    }

    public function reverseSort(): string
    {
        return $this->sortDirection === 'asc'
            ? 'desc'
            : 'asc';
    }

    public function applySorting($query)
    {
        if (is_a($query, Collection::class)) {
            return $query->sortBy($this->sortField, SORT_REGULAR, !(($this->sortDirection === 'asc')));
        }

        return $query->orderBy($this->sortField, $this->sortDirection);
    }
}
