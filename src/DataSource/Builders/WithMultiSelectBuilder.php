<?php

namespace PowerComponents\LivewirePowerGrid\DataSource\Builders;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Collection;

trait WithMultiSelectBuilder
{
    /** @param  list<string>|int|string|null  $values */
    public function builder(Builder|QueryBuilder $builder, string $field, array|int|string|null $values): void
    {
        if (data_get($this->filterBase, 'builder')) {
            /** @var Closure $closure */
            $closure = data_get($this->filterBase, 'builder');

            $closure($builder, $values);

            return;
        }

        if (! is_array($values)) {
            return;
        }

        $values = array_values(array_filter($values, fn ($value) => $value !== ''));

        if (count($values) === 0) {
            return;
        }

        $builder->whereIn($field, $values);
    }

    /** @param  list<string>|int|string|null  $values */
    public function collection(Collection $collection, string $field, array|int|string|null $values): Collection
    {
        if (data_get($this->filterBase, 'collection')) {
            /** @var Closure $closure */
            $closure = data_get($this->filterBase, 'collection');

            return $closure($collection, $values);
        }

        if (! is_array($values)) {
            return $collection;
        }

        $values = array_values(array_filter($values, fn ($value) => $value !== ''));

        if (count($values) === 0) {
            return $collection;
        }

        return $collection->whereIn($field, $values);
    }
}
