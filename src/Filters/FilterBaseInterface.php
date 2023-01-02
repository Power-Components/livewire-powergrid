<?php

namespace PowerComponents\LivewirePowerGrid\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

interface FilterBaseInterface
{
    public static function builder(Builder $query, string $field, array|int|string|null $values): void;

    public static function collection(Collection $builder, string $field, array|int|string|null $values): Collection;
}
