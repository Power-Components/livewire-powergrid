<?php

namespace PowerComponents\LivewirePowerGrid\Tests;

use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Rules\Rule;
use PowerComponents\LivewirePowerGrid\Tests\Models\Dish;

class RulesEmitToTable extends DishTableBase
{
    public array $eventId = [];

    protected function getListeners(): array
    {
        return array_merge(
            parent::getListeners(),
            [
                'deletedEvent',
            ]
        );
    }

    public function deletedEvent(array $params)
    {
        $this->eventId = $params;
    }

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
                ->when(fn (Dish $dish) => $dish->id == 5 || $dish->id == 6)
                ->emitTo('dishes-table', 'deletedEvent', ['dishId' => 'id']),
        ];
    }
}
