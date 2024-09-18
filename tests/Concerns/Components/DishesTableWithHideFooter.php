<?php

namespace PowerComponents\LivewirePowerGrid\Tests\Concerns\Components;

use PowerComponents\LivewirePowerGrid\{
    Exportable,
    Footer,
    Header
};

class DishesTableWithHideFooter extends DishesTable
{
    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            Exportable::make('export')
                ->striped()
                ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),

            Header::make()
                ->showToggleColumns()
                ->showSearchInput(),

            Footer::make()
                ->showPerPage(30)
                ->showRecordCount()
                ->hideIfResultIsMoreThenTotal(),
        ];
    }
}
