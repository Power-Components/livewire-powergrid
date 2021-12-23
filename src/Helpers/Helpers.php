<?php

namespace PowerComponents\LivewirePowerGrid\Helpers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use PowerComponents\LivewirePowerGrid\Button;

class Helpers
{
    public function makeParameters(Button $action, Model $entry): array
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
