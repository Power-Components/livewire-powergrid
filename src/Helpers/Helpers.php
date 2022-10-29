<?php

namespace PowerComponents\LivewirePowerGrid\Helpers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\{Arr, Str};

class Helpers
{
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

    public function resolveContent(string $currentTable, string $field, Model $row): ?string
    {
        $currentField = $field;
        $replace      = fn ($content) => preg_replace('#<script(.*?)>(.*?)</script>#is', '', $content);

        if (str_contains($currentField, '.')) {
            $data  = Str::of($field)->explode('.');
            $table = $data->get(0);
            $field = $data->get(1);

            if ($table === $currentTable) {
                return $replace($row->{$field});
            }

            return $replace($row->{$table}->{$field});
        }

        return $replace($row->{$field});
    }
}
