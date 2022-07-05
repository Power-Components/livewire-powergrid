<?php

namespace PowerComponents\LivewirePowerGrid\Tests;

use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Rules\Rule;
use PowerComponents\LivewirePowerGrid\Tests\Models\Dish;

class RulesBladeComponentTable extends DishTableBase
{
    public function actions(): array
    {
        return [
            Button::add('edit')
                ->caption('<div id="edit">Edit</div>')
                ->class('text-center'),
        ];
    }

    public function actionRules(): array
    {
        return [
            Rule::button('edit')
                ->when(fn (Dish $dish) => $dish->id == 5)
                ->bladeComponent('livewire-powergrid::icons.arrow', ['dish-id' => 'id']),
        ];
    }
}
