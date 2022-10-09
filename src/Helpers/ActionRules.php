<?php

namespace PowerComponents\LivewirePowerGrid\Helpers;

use Illuminate\Support\{Arr, Collection};
use PowerComponents\LivewirePowerGrid\Button;

class ActionRules
{
    protected array $actionRules = [
        'emit',
        'emitTo',
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

    public function resolveRules(array $rules, object|array $row): Collection
    {
        $rules   = collect($rules);

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

            return (object) ['rules.' . $index . '.' . $rule->forAction => $prepareRule];
        });
    }

    private function unDotRulesFromRow(\Illuminate\Database\Eloquent\Model|\stdClass|array $row): Collection
    {
        /** @phpstan-ignore-next-line */
        $unDottedRow = Arr::undot(collect($row)->toArray());

        $rules = (array) data_get($unDottedRow, 'rules', []);

        return collect($rules);
    }

    public function recoverFromAction(string $action, \Illuminate\Database\Eloquent\Model|\stdClass|array $row): array
    {
        $actionRules = [];

        $rules = $this->unDotRulesFromRow($row);

        $rules->each(function ($key) use (&$actionRules, $action) {
            $key = (array) $key;

            if (isset($key[$action])) {
                $rule = (array) $key[$action];
                foreach ($this->actionRules as $action) {
                    if (data_get($rule, "action.$action")) {
                        $actionRules[$action][] = data_get($rule, "action.$action");
                    }
                }
            }
        });

        return $actionRules;
    }

    public function recoverFromButton(Button $button, \Illuminate\Database\Eloquent\Model|\stdClass|array $row): array
    {
        $actionRules = [];

        $rules = $this->unDotRulesFromRow($row);

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
