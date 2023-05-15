<?php

namespace PowerComponents\LivewirePowerGrid\Filters\Builders;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\{Carbon, Collection};

class DateTimePicker extends BuilderBase
{
    public function builder(EloquentBuilder|QueryBuilder $builder, string $field, int|array|string|null $values): void
    {
        /** @var array $values */
        [$startDate, $endDate] = [
            0 => Carbon::parse($values[0]),
            1 => Carbon::parse($values[1]),
        ];

        if (data_get($this->filterBase, 'builder')) {
            /** @var \Closure $closure */
            $closure = data_get($this->filterBase, 'builder');

            $closure($builder, $values);

            return;
        }

        $builder->whereBetween($field, [$startDate, $endDate]);
    }

    public function collection(Collection $collection, string $field, int|array|string|null $values): Collection
    {
        /** @var array $values */
        [$startDate, $endDate] = [
            0 => Carbon::parse($values[0]),
            1 => Carbon::parse($values[1]),
        ];

        if (data_get($this->filterBase, 'collection')) {
            /** @var \Closure $closure */
            $closure = data_get($this->filterBase, 'collection');

            return $closure($collection, $values);
        }

        return $collection->whereBetween($field, [$startDate, $endDate]);
    }
}
