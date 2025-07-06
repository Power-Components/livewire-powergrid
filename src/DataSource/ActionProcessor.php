<?php

namespace PowerComponents\LivewirePowerGrid\DataSource;

use PowerComponents\LivewirePowerGrid\{Button, PowerGridComponent};
use stdClass;

final class ActionProcessor
{
    private bool $shouldProcessActions;

    private bool $shouldProcessActionRules;

    public function __construct(protected PowerGridComponent $component)
    {
        $this->shouldProcessActions     = method_exists($component, 'actions');
        $this->shouldProcessActionRules = method_exists($component, 'actionRules');
    }

    public function process(object $row, stdClass $loopVars): array
    {
        $actions = [];
        $rules   = [];

        if ($this->shouldProcessActionRules) {
            $rules = $this->component->prepareActionRulesForRows($row, $loopVars);
        }

        if ($this->shouldProcessActions) {
            /** @var array $actions */
            $actions = $this->component->actions($row);
            $actions = collect($actions)
                ->map(fn (Button $action) => $this->mapAction($action, $row))
                ->all();
        }

        return [
            'actions' => $actions,
            'rules'   => $rules,
        ];
    }

    private function mapAction(Button $action, object $row): array
    {
        $can = $action->can;

        return [
            'action'         => $action->action,
            'can'            => $can instanceof \Closure ? $can($row) : $can,
            'slot'           => $action->slot,
            'tag'            => $action->tag,
            'icon'           => $action->icon,
            'iconAttributes' => $action->iconAttributes,
            'attributes'     => $action->attributes,
        ];
    }
}
