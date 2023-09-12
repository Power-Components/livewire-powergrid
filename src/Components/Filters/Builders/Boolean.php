<?php

namespace PowerComponents\LivewirePowerGrid\Components\Filters\Builders;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Collection;

class Boolean extends BuilderBase
{
    public function builder(EloquentBuilder|QueryBuilder $builder, string $field, int|array|string|null $values): void
    {
        if (data_get($this->filterBase, 'builder')) {
            /** @var \Closure $closure */
            $closure = data_get($this->filterBase, 'builder');

            $closure($builder, $values);

            return;
        }

        if (is_null($values)) {
            $values = 'all';
        }

        if (is_array($values)) {
            $field  = $field . '.' . key($values);
            $values = $values[key($values)];
        }

        if ($values != 'all') {
            $values = ($values == 'true' || $values == '1');
            $builder->where($field, '=', $values);
        }
    }

    public function collection(Collection $collection, string $field, int|array|string|null $values): Collection
    {
        if (data_get($this->filterBase, 'collection')) {
            /** @var \Closure $closure */
            $closure = data_get($this->filterBase, 'collection');

            return $closure($collection, $values);
        }

        if (is_null($values)) {
            $values = 'all';
        }

        if ($values != 'all') {
            $values = ($values == 'true' || $values == '1');

            return $collection->where($field, '=', $values);
        }

        return $collection;
    }
}
