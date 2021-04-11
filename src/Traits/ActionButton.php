<?php

namespace PowerComponents\LivewirePowerGrid\Traits;

use PowerComponents\LivewirePowerGrid\Button;

trait ActionButton
{
    /**
     * @var array
     */
    public array $actionRoutes = [];
    /**
     * @var array
     */
    public array $actionBtns = [];

    public function initActions()
    {
        $this->actionBtns = $this->actions();

        foreach ($this->actionBtns as $actionBtn) {
            if (isset($actionBtn->route)) {
                $this->actionRoutes[$actionBtn->action] = $actionBtn->route;
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
