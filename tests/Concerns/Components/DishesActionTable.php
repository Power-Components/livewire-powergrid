<?php

namespace PowerComponents\LivewirePowerGrid\Tests\Concerns\Components;

class DishesActionTable extends DishTableBase
{
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
}
