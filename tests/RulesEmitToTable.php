<?php

namespace PowerComponents\LivewirePowerGrid\Tests;

class RulesEmitToTable extends DishTableBase
{
    public array $eventId = [];

    public array $testActions = [];

    public array $testActionRules = [];

    public function actions(): array
    {
        return $this->testActions;
    }

    public function actionRules(): array
    {
        return $this->testActionRules;
    }

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
}
