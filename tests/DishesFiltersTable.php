<?php

namespace PowerComponents\LivewirePowerGrid\Tests;

use PowerComponents\LivewirePowerGrid\Traits\ActionButton;

class DishesFiltersTable extends DishTableBase
{
    use ActionButton;

    public array $testFilters = [];

    public function filters(): array
    {
        return $this->testFilters;
    }
}
