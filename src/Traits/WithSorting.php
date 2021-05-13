<?php

namespace PowerComponents\LivewirePowerGrid\Traits;

use Illuminate\Support\Collection;

trait WithSorting
{
    public string $sortField = 'id';

    public string $sortDirection = 'asc';

    public string $sortIcon = '&#8597;';

    public string $sortAscIcon = '&#8593;';

    public string $sortDescIcon = '&#8595;';

    public function sortBy($field)
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
            return $query->sortBy($this->sortField, SORT_REGULAR, $this->sortDirection);
        }

        return $query->orderBy($this->sortField, $this->sortDirection);
    }
}
