<?php

namespace PowerComponents\LivewirePowerGrid\Tests\Concerns\Components;

use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;

class DishesCustomPageNameTable extends DishTableBase
{
    public string $tableName = 'testing-dishes-custom-perpage-table';

    public string $pageNameCandidate;

    public function setUp(): array
    {
        return [
            ...parent::setup(),
            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount()
                ->pageName($this->pageNameCandidate),
        ];
    }
}
