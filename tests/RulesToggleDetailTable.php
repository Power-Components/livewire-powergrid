<?php

namespace PowerComponents\LivewirePowerGrid\Tests;

use PowerComponents\LivewirePowerGrid\{
    Detail,
    Footer,
    Header};

class RulesToggleDetailTable extends DishTableBase
{
    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            Header::make()
                ->showSearchInput(),

            Footer::make()
                ->showPerPage(5)
                ->showRecordCount(),

            Detail::make()
                ->view('livewire-powergrid::tests.detail')
                ->options([
                    'name' => 'Luan',
                ])
                ->showCollapseIcon(),
        ];
    }
}
