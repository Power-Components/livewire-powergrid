<?php

namespace PowerComponents\LivewirePowerGrid\Components\Rules;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Traits\UnDot;

class RulesController
{
    use UnDot;

    public function execute(array $rules, object|array $row): Collection
    {
        $rules = collect($rules);

        /** @phpstan-ignore-next-line */
        return $rules->mapWithKeys(function ($rule, $index) use ($row) {
            $resolveRules = true;

            if (isset($rule->rule['when'])) {
                $resolveRules = $rule->rule['when']((object) $row);
            }

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

            return (object) [$rule->forAction . '.' . $index => $prepareRule];
        });
    }

    public function recoverFromAction(Model|\stdClass|array $row, $filterAction = null): array
    {
        $actionRules = collect();

        $rules = $this->unDotActionsFromRow($row, 'rules');

        $rules->each(function ($rule, $target) use ($actionRules, $filterAction) {
            if (str_contains($target, $filterAction)) {
                $actionRules->push(data_get($rule, "action"));
            }
        });

        $filterRulesByKey = fn ($ruleKey) => array_filter(
            $actionRules
                ->map(fn ($item) => $item[$ruleKey] ?? null)
                ->values()
                ->toArray(),
            fn ($value) => !is_null($value),
        );

        return [
            'setAttributes' => $filterRulesByKey('setAttribute'),
            'disable'      => $filterRulesByKey('disable'),
            'hide'          => $filterRulesByKey('hide'),
        ];
    }

    public function recoverFromButton(Button $button, Model|\stdClass|array $row): array
    {
        $actionRules = [];

        $rules = $this->unDotActionsFromRow($row, 'rules');

        $rules->each(function ($key) use (&$actionRules, $button) {
            $key = (array) $key;

            if (isset($key[$button->action])) {
                $rule = (array) $key[$button->action];

                //                foreach ($this->actionRules as $action) {
                //                    if (data_get($rule, "action.$action")) {
                //                        $actionRules[$action] = data_get($rule, "action.$action");
                //                    }
                //                }
            }
        });

        return $actionRules;
    }

    public function loop(array $actionRules, object $loop): bool
    {
        foreach ($actionRules as $actionRule) {
            if (isset($actionRule->rule['loop'])) {
                return $actionRule->rule['loop']($loop);
            }
        }

        return true;
    }
}
