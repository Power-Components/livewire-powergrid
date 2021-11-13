<?php

namespace PowerComponents\LivewirePowerGrid\Services\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection as BaseCollection;
use PowerComponents\LivewirePowerGrid\Helpers\{Collection, Model};

interface FilterInterface
{
    /**
     * @param Builder | BaseCollection $query
     * @return Collection | Model
     */
    public static function query($query);

    /** @return Collection | Model */
    public function setColumns(array $columns);

    /** @return Collection | Model */
    public function setSearch(string $search);

    /** @return Collection | Model */
    public function setFilters(array $filters);

    /** @return Builder | Model | BaseCollection */
    public function filter();
}
