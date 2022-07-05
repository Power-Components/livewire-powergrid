<?php

namespace PowerComponents\LivewirePowerGrid\Helpers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\{Arr, Str};
use PowerComponents\LivewirePowerGrid\Button;

class Helpers
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
    ];

    public function makeActionParameters(array $params = [], Model|\stdClass|null $row = null): array
    {
        $parameters = [];

        foreach ($params as $param => $value) {
            if ($row && filled($row->{$value})) {
                $parameters[$param] = $row->{$value};
            } else {
                $parameters[$param] = $value;
            }
        }

        return $parameters;
    }

    public function makeActionParameter(array $params = [], Model|\stdClass|null $row = null): array
    {
        return $this->makeActionParameters($params, $row)[0];
    }

    public function makeActionRules(string|Button $action, Model|\stdClass|null|array $row): array
    {
        $actionRules = [];

        /** @phpstan-ignore-next-line */
        $row = Arr::undot(collect($row)->toArray());

        $rules = data_get($row, 'rules');

        if (blank($rules)) {
            return [];
        }

        /** @phpstan-ignore-next-line */
        collect($rules)->each(function ($key) use (&$actionRules, $action) {
            $key = (array) $key;

            if ($action instanceof Button) {
                if (isset($key[$action->action])) {
                    $rule = (array) $key[$action->action];

                    foreach ($this->actionRules as $action) {
                        if (data_get($rule, "action.$action")) {
                            $actionRules[$action] = data_get($rule, "action.$action");
                        }
                    }
                }
            }

            if (is_string($action)) {
                if (isset($key[$action])) {
                    $rule = (array) $key[$action];
                    foreach ($this->actionRules as $action) {
                        if (data_get($rule, "action.$action")) {
                            /** @phpstan-ignore-next-line  */
                            $actionRules[$action][] = data_get($rule, "action.$action");
                        }
                    }
                }
            }
        });

        return $actionRules;
    }

    public function resolveContent(string $currentTable, string $field, Model $row): ?string
    {
        $currentField = $field;
        $replace      = fn ($content) => preg_replace('#<script(.*?)>(.*?)</script>#is', '', $content);

        if (str_contains($currentField, '.')) {
            $data  = Str::of($field)->explode('.');
            $table = $data->get(0);
            $field = $data->get(1);

            if ($table === $currentTable) {
                return $replace(addslashes($row->{$field}));
            }

            return $replace(addslashes($row->{$table}->{$field}));
        }

        return $replace(addslashes($row->{$field}));
    }

    /**
     * @param array|object $row
     */
    public function resolveRules(array $rules, $row): \Illuminate\Support\Collection
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
}
