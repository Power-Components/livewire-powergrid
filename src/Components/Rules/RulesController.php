<?php

namespace PowerComponents\LivewirePowerGrid\Components\Rules;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use PowerComponents\LivewirePowerGrid\Components\Concerns\UnDot;

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

            if (isset($rule->rule['unless'])) {
                $resolveRules = !$rule->rule['unless']((object) $row);
            }

            $prepareRule = [
                /** @phpstan-ignore-next-line */
                'action' => collect($rule->rule)
                    ->forget(['when', 'action.redirect.closure'])
                    ->forget(['unless', 'action.redirect.closure'])
                    ->toArray(),
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

    public function recoverFromAction(Model|\stdClass|array $row, string $filterAction = ''): array
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

        /**
         * Ensures every modifier is handled here *  and avoids Breaking changes in V5
         */
        return collect(RuleManager::applicableModifiers())
                 ->mapWithKeys(function ($rule) use ($filterRulesByKey) {
                     if ($rule === 'setAttribute') {
                         return  ['setAttributes' => $filterRulesByKey($rule)];
                     }

                     return [$rule => $filterRulesByKey($rule)];
                 })-> toArray();
    }

    public function loop(array $actionRules, object|int $loop): bool
    {
        foreach ($actionRules as $actionRule) {
            if (isset($actionRule->rule['loop'])) {
                return $actionRule->rule['loop']($loop);
            }
        }

        return true;
    }
}
