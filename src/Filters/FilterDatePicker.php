<?php

namespace PowerComponents\LivewirePowerGrid\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\{Carbon, Collection};

class FilterDatePicker implements FilterBaseInterface
{
    use WithFilterBase;

    public array $params = [];

    public function params(array $params): FilterDatePicker
    {
        $this->params = $params;

        return $this;
    }

    public static function builder(Builder $query, string $field, int|array|string|null $values): void
    {
        /** @var array $values */
        if (isset($values[0]) && isset($values[1])) {
            $query->whereBetween($field, [Carbon::parse($values[0]), Carbon::parse($values[1])]);
        }
    }

    public static function collection(Collection $builder, string $field, int|array|string|null $values): Collection
    {
        /** @var array $values */
        if (isset($values[0]) && isset($values[1])) {
            return $builder->whereBetween($field, [Carbon::parse($values[0]), Carbon::parse($values[1])]);
        }

        return $builder;
    }
}
