<?php

namespace PowerComponents\LivewirePowerGrid\Tests;

use PowerComponents\LivewirePowerGrid\Tests\Models\Dish;
use PowerComponents\LivewirePowerGrid\{Button,Rules\Rule
};

class RulesCaptionTable extends DishTableBase
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
                ->when(fn (Dish $dish) => $dish->id == 4)
                ->caption('Cation Edit for id 4'),
        ];
    }
}
