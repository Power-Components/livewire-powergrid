<?php

namespace PowerComponents\LivewirePowerGrid\Components\Filters\Builders;

use Illuminate\Database\Eloquent\{Builder};
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Collection;

trait WithMultiSelectBuilder
{
    public function builder(Builder|QueryBuilder $builder, string $field, array|int|string|null $values): void
    {
        if (data_get($this->filterBase, 'builder')) {
            /** @var \Closure $closure */
            $closure = data_get($this->filterBase, 'builder');

            $closure($builder, $values);

            return;
        }

        $empty = false;

        /** @var array $values */
        if (count($values) === 0) {
            return;
        }

        foreach ($values as $value) {
            if ($value === '') {
                $empty = true;
            }
        }

        if (!$empty) {
            $builder->whereIn($field, $values);
        }
    }

    public function collection(Collection $collection, string $field, array|int|string|null $values): Collection
    {
        if (data_get($this->filterBase, 'collection')) {
            /** @var \Closure $closure */
            $closure = data_get($this->filterBase, 'collection');

            return $closure($collection, $values);
        }

        /** @var array $values */
        $empty = false;

        if (!is_array($values) || count($values) === 0) {
            return $collection;
        }

        foreach ($values as $value) {
            if ($value === '') {
                $empty = true;
            }
        }

        if ($empty) {
            return $collection;
        }

        return $collection->whereIn($field, $values);
    }
}
