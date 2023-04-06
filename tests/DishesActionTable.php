<?php

namespace PowerComponents\LivewirePowerGrid\Tests;

use PowerComponents\LivewirePowerGrid\Traits\ActionButton;

class DishesActionTable extends DishTableBase
{
    use ActionButton;

    public array $eventId = [];

    public bool $join = false;

    public array $actionsTest = [];

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

    public function openModal(array $params)
    {
        $this->eventId = $params;
    }

    public function actions(): array
    {
        return $this->actionsTest;
    }
}
