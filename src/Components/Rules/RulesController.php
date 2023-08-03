<?php

namespace PowerComponents\LivewirePowerGrid\Components\Rules;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Traits\UnDot;

class RulesController
{
    use UnDot;

    protected array $actionRules = [
        'dispatch',
        'dispatchTo',
        'setAttribute',
        'disable',
        'hide',
        'redirect',
        'caption',
        'pg:rows',
        'pg:column',
        'detailView',
        'bladeComponent',
        'showHideToggleable',
    ];

    public function execute(array $rules, object|array $row): Collection
    {
        $rules = collect($rules);

        /** @phpstan-ignore-next-line */
        return $rules->mapWithKeys(function ($rule, $index) use ($row) {
            $resolveRules = $rule->rule['when']((object) $row);

            $prepareRule = [
                /** @phpstan-ignore-next-line */
                'action' => collect($rule->rule)->forget(['when', 'action.redirect.closure'])->toArray(),
            ];

            if (data_get($rule->rule, 'redirect') && $resolveRules === true) {
                data_set($prepareRule, 'action.redirect.url', $rule->rule['redirect']['closure']((object) $row));
            }

            if ($resolveRules === false) {
                $prepareRule = [];
            }

            return (object) [$rule->forAction => $prepareRule];
        });
    }

    public function recoverFromAction(string $action, Model|\stdClass|array $row): array
    {
        $actionRules = [];

        $rules = $this->unDotActionsFromRow($row, 'rules');

        $rules->each(function ($rule) use (&$actionRules, $action) {
            foreach ($this->actionRules as $action) {
                if (data_get($rule, "action.$action")) {
                    $actionRules[$action][] = data_get($rule, "action.$action");
                }
            }
        });

        return $actionRules;
    }

    public function recoverFromButton(Button $button, Model|\stdClass|array $row): array
    {
        $actionRules = [];

        $rules = $this->unDotActionsFromRow($row, 'rules');

        $rules->each(function ($key) use (&$actionRules, $button) {
            $key = (array) $key;

            if (isset($key[$button->action])) {
                $rule = (array) $key[$button->action];

                foreach ($this->actionRules as $action) {
                    if (data_get($rule, "action.$action")) {
                        $actionRules[$action] = data_get($rule, "action.$action");
                    }
                }
            }
        });

        return $actionRules;
    }
}
