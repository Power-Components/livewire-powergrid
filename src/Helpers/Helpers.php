<?php

namespace PowerComponents\LivewirePowerGrid\Helpers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\{Arr, Str};
use PowerComponents\LivewirePowerGrid\{Button};

class Helpers
{
    protected array $actions = [
        'wire',
        'setAttribute',
        'disable',
    ];

    public function makeActionParameters(Button $action, Model $entry): array
    {
        $parameters = [];
        foreach ($action->param as $param => $value) {
            if (!empty($entry->{$value})) {
                $parameters[$param] = $entry->{$value};
            } else {
                $parameters[$param] = $value;
            }
        }

        return $parameters;
    }

    public function makeParameters(array $params, Model $entry): array
    {
        $parameters = [];
        foreach ($params as $param => $value) {
            if (!empty($entry->{$value})) {
                $parameters[$param] = $entry->{$value};
            } else {
                $parameters[$param] = $value;
            }
        }

        return $parameters;
    }

    public function makeActionRules(Button $action, Model $entry): array
    {
        $actionRules = [];

        $rules = collect(data_get(Arr::undot($entry->toArray()), 'rules'));

        $rules->each(function ($key) use (&$actionRules, $action) {
            $key = (array) $key;
            if (isset($key[$action->action])) {
                $rule = (array) $key[$action->action];

                foreach ($this->actions as $action) {
                    if (data_get($rule, "action.$action") && $rule['applyRule']) {
                        $actionRules[$action] = data_get($rule, "action.$action");
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
}
