<?php

namespace PowerComponents\LivewirePowerGrid\Services\Contracts;

use Illuminate\Support\Collection as BaseCollection;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Helpers\Collection;

interface CollectionFilterInterface
{
    public static function query(BaseCollection $query): Collection;

    /**
     * @param array<Column> $columns
     */
    public function setColumns(array $columns): Collection;

    public function setSearch(string $search): Collection;

    /**
     * @param array<string, array<string>> $filters
     */
    public function setFilters(array $filters): Collection;

    public function filter(): BaseCollection;
}
