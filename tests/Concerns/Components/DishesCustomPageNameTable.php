<?php

namespace PowerComponents\LivewirePowerGrid\Tests\Concerns\Components;

use PowerComponents\LivewirePowerGrid\Footer;

class DishesCustomPageNameTable extends DishTableBase
{
    public array $testFilters = [];

    public function setUp(): array
    {
        return [...parent::setup(),
            Footer::make()
                ->showPerPage()
                ->showRecordCount()
                ->pageName('customPage12Ντόναλντ34'),
        ];
    }
}
