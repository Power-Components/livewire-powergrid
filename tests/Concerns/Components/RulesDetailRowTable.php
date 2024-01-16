<?php

namespace PowerComponents\LivewirePowerGrid\Tests\Concerns\Components;

use PowerComponents\LivewirePowerGrid\Facades\Rule;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Models\Dish;
use PowerComponents\LivewirePowerGrid\{Button, Detail, Footer, Header};

class RulesDetailRowTable extends DishTableBase
{
    public function setUp(): array
    {
        config()->set('livewire.inject_morph_markers', false);

        return [
            Header::make()
                ->showToggleColumns()
                ->showSearchInput(),

            Footer::make()
                ->showPerPage()
                ->showRecordCount(),

            Detail::make()
                ->view('livewire-powergrid::tests.detail')
                ->options(['name' => 'Luan'])
                ->showCollapseIcon(),
        ];
    }

    public function actions(): array
    {
        return [
            Button::add('edit')
                ->slot('<div id="edit">Toggle</div>')
                ->class('text-center')
                ->toggleDetail(),
        ];
    }

    public function actionRules(): array
    {
        return [
            Rule::rows()
                ->when(fn (Dish $dish) => $dish->id == 1)
                ->detailView('components.detail-rules', ['test' => 1]),
        ];
    }
}
