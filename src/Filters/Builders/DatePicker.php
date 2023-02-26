<?php

namespace PowerComponents\LivewirePowerGrid\Filters\Builders;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\{Carbon, Collection};

class DatePicker extends BuilderBase
{
    public function builder(Builder $builder, string $field, int|array|string|null $values): void
    {
        if (data_get($this->filterBase, 'builder')) {
            /** @var \Closure $closure */
            $closure = data_get($this->filterBase, 'builder');

            $closure($builder, $values);

            return;
        }

        /** @var array $values */
        if (isset($values[0]) && isset($values[1])) {
            $builder->whereBetween($field, [Carbon::parse($values[0]), Carbon::parse($values[1])]);
        }
    }

    public function collection(Collection $collection, string $field, int|array|string|null $values): Collection
    {
        if (data_get($this->filterBase, 'collection')) {
            /** @var \Closure $closure */
            $closure = data_get($this->filterBase, 'collection');

            return $closure($collection, $values);
        }

        /** @var array $values */
        if (isset($values[0]) && isset($values[1])) {
            return $collection->whereBetween($field, [Carbon::parse($values[0]), Carbon::parse($values[1])]);
        }

        return $collection;
    }
}
