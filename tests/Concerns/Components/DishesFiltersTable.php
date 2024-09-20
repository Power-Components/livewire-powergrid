<?php

namespace PowerComponents\LivewirePowerGrid\Tests\Concerns\Components;

class DishesFiltersTable extends DishTableBase
{
    public string $tableName = 'testing-dishes-filters-table';

    public array $testFilters = [];

    public function filters(): array
    {
        return $this->testFilters;
    }
}
