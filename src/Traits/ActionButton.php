<?php

namespace PowerComponents\LivewirePowerGrid\Traits;

trait ActionButton
{
    /**
     * @var array
     */
    public array $actionRoutes = [];

    /**
     * @var array
     */
    public array $actions = [];

    public function initActions()
    {
        $this->actions = $this->actions();

        foreach ($this->actions as $action) {
            if (isset($action->route)) {
                $this->actionRoutes[$action->action] = $action->route;
            }
        }
    }

    /**
     * @return array
     */
    public function actions(): array
    {
        return [];
    }
}
