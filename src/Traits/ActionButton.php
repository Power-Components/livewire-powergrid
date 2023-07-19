<?php

namespace PowerComponents\LivewirePowerGrid\Traits;

use PowerComponents\LivewirePowerGrid\Button;

trait ActionButton
{
    public array $actionRoutes = [];

    public array $actionHeader = [];

    public array $actions = [];

    public function initActions()
    {
        $this->actions = collect($this->actions())
            ->where('can', true)
            ->toArray();

        /** @var Button $action */
        foreach ($this->actions as $action) {
            unset($action->render);

            if (is_callable(data_get($action, 'params'))) {
                data_set($action, 'params', []);
            }

            if (isset($action->route)) {
                $this->actionRoutes[data_get($action, 'action')] = data_get($action, 'route');
            }
        }
    }

    public function actions(): array
    {
        return [];
    }

    public function header(): array
    {
        return [];
    }
}
