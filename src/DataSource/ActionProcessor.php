<?php

namespace PowerComponents\LivewirePowerGrid\DataSource;

use PowerComponents\LivewirePowerGrid\{Button, Contracts\PowerGridContext};

final class ActionProcessor
{
    private bool $shouldProcessActions;

    private bool $shouldProcessActionRules;

    public function __construct(protected PowerGridContext $component)
    {
        $this->shouldProcessActions = method_exists($component, 'actions');
        $this->shouldProcessActionRules = method_exists($component, 'actionRules');
    }

    /**
     * @return array<int, array{action: string, can: mixed, slot: ?string, tag: ?string, icon: ?string, iconAttributes: array<string, mixed>, attributes: array<string, mixed>, rules: array<int, array<string, mixed>>}>
     */
    public function process(object $row): array
    {
        $actions = [];

        if ($this->shouldProcessActions) {
            /** @var list<Button> $actions */
            $actions = $this->component->actions($row);

            $rules = $this->shouldProcessActionRules
                ? $this->component->resolveActionRules($row)
                : [];

            $actions = collect($actions)
                ->map(fn (Button $action) => $this->mapAction($action, $row, $rules))
                ->all();
        }

        return $actions;
    }

    /**
     * @param  array<int, array<string, mixed>>  $rules
     * @return array{action: string, can: mixed, slot: ?string, tag: ?string, icon: ?string, iconAttributes: array<string, mixed>, attributes: array<string, mixed>, rules: array<int, array<string, mixed>>}
     */
    private function mapAction(Button $action, object $row, array $rules): array
    {
        $can = $action->can;

        return [
            'action' => $action->action,
            'can' => $can instanceof \Closure ? $can($row) : $can,
            'slot' => $action->slot,
            'tag' => $action->tag,
            'icon' => $action->icon,
            'iconAttributes' => $action->iconAttributes,
            'attributes' => $action->attributes,
            'rules' => $rules,
        ];
    }
}
