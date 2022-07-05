<?php

namespace PowerComponents\LivewirePowerGrid\Tests;

use PowerComponents\LivewirePowerGrid\Tests\Models\Dish;
use PowerComponents\LivewirePowerGrid\{Button,Rules\Rule
};

class RulesHideTable extends DishTableBase
{
    public function actions(): array
    {
        return [
            Button::add('edit')
                ->caption('<div id="edit">Edit</div>')
                ->class('text-center')
                ->openModal('modal-edit', ['dishId' => 'id']),
        ];
    }

    public function actionRules(): array
    {
        return [
            Rule::button('edit')
                ->when(fn (Dish $dish) => $dish->id == 2)
                ->hide(),
        ];
    }
}
