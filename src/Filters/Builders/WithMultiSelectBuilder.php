<?php

namespace PowerComponents\LivewirePowerGrid\Filters\Builders;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

trait WithMultiSelectBuilder
{
    public static function builder(Builder $builder, string $field, array|int|string|null $values): void
    {
        $empty = false;

        /** @var array $values */
        foreach ($values as $value) {
            if ($value === '') {
                $empty = true;
            }
        }

        if (!$empty) {
            $builder->whereIn($field, $values);
        }
    }

    public static function collection(Collection $builder, string $field, array|int|string|null $values): Collection
    {
        /** @var array $values */
        $empty  = false;
        $values = collect($values)->get('values');

        if (!is_array($values) || count($values) === 0) {
            return $builder;
        }

        foreach ($values as $value) {
            if ($value === '') {
                $empty = true;
            }
        }

        if ($empty) {
            return $builder;
        }

        return $builder->whereIn($field, $values);
    }
}
