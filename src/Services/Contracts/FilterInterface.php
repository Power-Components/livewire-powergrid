<?php

namespace PowerComponents\LivewirePowerGrid\Services\Contracts;

interface FilterInterface
{
    public static function query($query);

    public function setColumns(array $columns);

    public function setSearch(string $search);

    public function setFilters(array $filters);

    public function filter();
}
