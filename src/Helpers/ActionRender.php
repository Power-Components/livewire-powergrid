<?php

namespace PowerComponents\LivewirePowerGrid\Helpers;

use Illuminate\Support\{Arr, Collection};
use PowerComponents\LivewirePowerGrid\Button;

class ActionRender
{
    public function resolveActionRender(array $actions, object|array $row): Collection
    {
        $rules = collect($actions);

        /** @phpstan-ignore-next-line */
        return $rules->mapWithKeys(function (Button $action, $index) use ($row) {
            $render = $action->render ?? null;
            $params = $action->params;

            if (is_callable($params)) {
                $resolveParams = $params((object) $row);

                return (object) ['render-action.' . $index . '.' . $action->action => $resolveParams];
            }

            if ($render) {
                $resolveAction = $render((object) $row);

                return (object) ['render-action.' . $index . '.' . $action->action => $resolveAction];
            }

            return (object) ['render-action.' . $index . '.' . $action->action => null];
        });
    }

    private function unDotActionsFromRow(\Illuminate\Database\Eloquent\Model|\stdClass|array $row): Collection
    {
        /** @phpstan-ignore-next-line */
        $unDottedRow = Arr::undot(collect($row)->toArray());

        $actions = (array) data_get($unDottedRow, 'render-action', []);

        return collect($actions);
    }

    public function recoverFromButton(Button $button, \Illuminate\Database\Eloquent\Model|\stdClass|array $row): array
    {
        $actionRules = [];

        $actions = $this->unDotActionsFromRow($row);

        $actions->each(function ($key) use (&$actionRules, $button) {
            $key = (array) $key;

            if (isset($key[$button->action])) {
                $action = (array) $key[$button->action];

                if (isset($action[0])) {
                    $actionRules['custom-action'] = $action[0];
                } else {
                    $actionRules['custom-action'] = $action;
                }
            }
        });

        return $actionRules;
    }
}
