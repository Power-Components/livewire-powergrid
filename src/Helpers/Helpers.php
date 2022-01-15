<?php

namespace PowerComponents\LivewirePowerGrid\Helpers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\{Arr, Str};
use PowerComponents\LivewirePowerGrid\{Button, Rule};

class Helpers
{
    protected array $actions = [
        'emit',
        'setAttribute',
        'disable',
        'hide',
        'redirect',
        'caption',
        'pg:row',
        'pg:column',
    ];

    public function makeActionParameters(array $params = [], ?Model $entry = null): array
    {
        $parameters = [];

        foreach ($params as $param => $value) {
            if ($entry && filled($entry->{$value})) {
                $parameters[$param] = $entry->{$value};
            } else {
                $parameters[$param] = $value;
            }
        }

        return $parameters;
    }

    /**
     * @param string|Button $action
     * @param \stdClass $entry
     * @return array
     */
    public function makeActionRules($action, \stdClass $entry): array
    {
        $actionRules = [];

        $rules = collect(data_get(Arr::undot(collect($entry)->toArray()), 'rules'));

        $rules->each(function ($key) use (&$actionRules, $action) {
            $key = (array) $key;

            if ($action instanceof Button) {
                if (isset($key[$action->action])) {
                    $rule = (array) $key[$action->action];

                    foreach ($this->actions as $action) {
                        if (data_get($rule, "action.$action") && $rule['applyRule']) {
                            $actionRules[$action] = data_get($rule, "action.$action");
                        }
                    }
                }
            }

            if (is_string($action)) {
                if (isset($key[$action])) {
                    $rule = (array) $key[$action];
                    foreach ($this->actions as $action) {
                        if (data_get($rule, "action.$action") && $rule['applyRule']) {
                            $actionRules[$action] = data_get($rule, "action.$action");
                        }
                    }
                }
            }
        });

        return $actionRules;
    }

    public function resolveContent(string $currentTable, string $field, Model $entry): ?string
    {
        $currentField = $field;

        if (str_contains($currentField, '.')) {
            $data  = Str::of($field)->explode('.');
            $table = $data->get(0);
            $field = $data->get(1);

            if ($table === $currentTable) {
                $content = addslashes($entry->{$field});
            } else {
                $content = addslashes($entry->{$table}->{$field});
            }
        } else {
            $content = addslashes($entry->{$field});
        }

        return preg_replace('#<script(.*?)>(.*?)</script>#is', '', $content);
    }

    /**
     * @param array|object $row
     */
    public function resolveRules(array $rules, $row): \Illuminate\Support\Collection
    {
        $rules   = collect($rules);

        /** @phpstan-ignore-next-line */
        return $rules->mapWithKeys(function ($rule, $index) use ($row) {
            return (object) ['rules.' . $index . '.' . $rule->forAction => [
                'applyRule'  => $rule->rule['when']((object) $row),
                'action'     => collect($rule->rule)->forget('when')->toArray(),
                'attributes' => [
                    'type'   => $rule->type,
                    'column' => $rule->column,
                ],
            ]];
        });
    }
}
