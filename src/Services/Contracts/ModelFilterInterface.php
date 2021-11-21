<?php

namespace PowerComponents\LivewirePowerGrid\Services\Contracts;

use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Helpers\Model;

interface ModelFilterInterface
{
    public static function query(Builder $query): Model;

    /**
     * @param array<Column> $columns
     */
    public function setColumns(array $columns): Model;

    public function setSearch(string $search): Model;

    /**
     * @param array<string, array<string>> $filters
     */
    public function setFilters(array $filters): Model;

    public function filter(): Builder;
}
