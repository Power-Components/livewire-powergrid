<?php

namespace PowerComponents\LivewirePowerGrid\Tests;

class DishesFiltersTable extends DishTableBase
{
    public array $testFilters = [];

    public function filters(): array
    {
        return $this->testFilters;
    }
}
