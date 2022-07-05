<?php

namespace PowerComponents\LivewirePowerGrid\Tests;

use PowerComponents\LivewirePowerGrid\Tests\Models\Dish;
use PowerComponents\LivewirePowerGrid\{Button,
    Detail,
    Footer,
    Header,
    Rules\Rule};

class RulesToggleDetailTable extends DishesTable
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

    public function actions(): array
    {
        return [
            Button::make('toggleDetail', 'Toggle Detail')
                ->class('text-center')
                ->toggleDetail(),
        ];
    }

    public function actionRules(): array
    {
        return [
            Rule::rows()
                ->when(fn (Dish $dish) => $dish->id == 3)
                ->detailView('livewire-powergrid::tests.detail-rules', ['fromActionRule' => true]),
        ];
    }
}
