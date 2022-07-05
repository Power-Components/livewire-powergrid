<?php

namespace PowerComponents\LivewirePowerGrid\Tests;

use PowerComponents\LivewirePowerGrid\Tests\Models\Dish;
use PowerComponents\LivewirePowerGrid\{Button,Rules\Rule
};

class RulesAttributesTable extends DishTableBase
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
                ->when(fn (Dish $dish) => $dish->id == 2)
                ->setAttribute('class', 'bg-slate-200')
                ->setAttribute('title', 'Title changed by setAttributes when id 2')
                ->setAttribute('wire:click', ['test', ['param1' => 2, 'dishId' => 'id']]),
            Rule::button('edit')
                ->when(fn (Dish $dish) => $dish->id == 5)
                ->setAttribute('class', 'bg-slate-500')
                ->setAttribute('title', 'Title changed by setAttributes when id 5')
                ->setAttribute('wire:click', ['test', ['param1' => 5, 'dishId' => 'id']]),
        ];
    }
}
