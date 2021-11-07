<?php

namespace PowerComponents\LivewirePowerGrid\Services\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection as BaseCollection;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Helpers\Collection;
use PowerComponents\LivewirePowerGrid\Helpers\Model;

interface FilterInterface
{
    public static function query(BaseCollection|Builder $query): Collection|Model;

    /**
     * @param array<Column> $columns
     */
    public function setColumns(array $columns): Collection|Model;

    public function setSearch(string $search): Collection|Model;

    /**
     * @param array<string, array<string>> $filters
     */
    public function setFilters(array $filters): Collection|Model;

    public function filter(): BaseCollection|Builder;
}
