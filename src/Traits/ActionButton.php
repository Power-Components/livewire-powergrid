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
        return [
            Button::add('edit')
                ->caption(__('Editar'))
                ->class('btn btn-primary')
                ->route('user.edit', ['id' => 'id']),

            Button::add('delete')
                ->caption(__('Excluir'))
                ->class('btn btn-danger')
                ->route('user.edit', ['id' => 'id']),
        ];
    }

    /**
     * @param string $action
     * @param string $params
     * @param string $url
     */
    public function actionCall(string $action, string $params, string $url = '')
    {

        $this->redirect(route(\Arr::get($this->actionRoutes, $action), json_decode($params, true)));

    }
}
