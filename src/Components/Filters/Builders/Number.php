<?php

namespace PowerComponents\LivewirePowerGrid\Components\Filters\Builders;

use Illuminate\Database\Eloquent\{Builder};
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Collection;

class Number extends BuilderBase
{
    public function builder(Builder|QueryBuilder $builder, string $field, int|array|string|null $values): void
    {
        $thousands = data_get($this->filterBase, 'thousands');
        $decimal   = data_get($this->filterBase, 'decimal');

        if (data_get($this->filterBase, 'builder')) {
            /** @var \Closure $closure */
            $closure = data_get($this->filterBase, 'builder');

            $closure($builder, $values);

            return;
        }

        /** @var array $values */
        if (isset($values['start']) && !isset($values['end'])) {
            $start = $values['start'];

            if (is_string($thousands)) {
                $start = str_replace($thousands, '', $start);
            }

            if (is_string($decimal)) {
                $start = str_replace($decimal, '.', $start);
            }

            $builder->where($field, '>=', $start);
        }

        if (!isset($values['start']) && isset($values['end'])) {
            $end = $values['end'];

            if (is_string($thousands)) {
                $end = str_replace($thousands, '', $values['end']);
            }

            if (is_string($decimal)) {
                $end = (float) str_replace($decimal, '.', $end);
            }

            $builder->where($field, '<=', $end);
        }

        if (isset($values['start']) && isset($values['end'])) {
            $start = $values['start'];
            $end   = $values['end'];

            if (is_string($thousands)) {
                $start = str_replace($thousands, '', $values['start']);
                $end   = str_replace($thousands, '', $values['end']);
            }

            if (is_string($decimal)) {
                $start = str_replace($decimal, '.', $start);
                $end   = str_replace($decimal, '.', $end);
            }

            $builder->whereBetween($field, [$start, $end]);
        }
    }

    public function collection(Collection $collection, string $field, int|array|string|null $values): Collection
    {
        $thousands = data_get($this->filterBase, 'thousands');
        $decimal   = data_get($this->filterBase, 'decimal');

        if (data_get($this->filterBase, 'collection')) {
            /** @var \Closure $closure */
            $closure = data_get($this->filterBase, 'collection');

            return $closure($collection, $values);
        }

        /** @var array $values */
        if (isset($values['start']) && !isset($values['end'])) {
            $start = $values['start'];

            if (is_string($thousands)) {
                $start = str_replace($thousands, '', $values['start']);
            }

            if (is_string($decimal)) {
                $start = (float) str_replace($decimal, '.', $start);
            }

            return $collection->where($field, '>=', $start);
        }

        if (!isset($values['start']) && isset($values['end'])) {
            $end = $values['end'];

            if (is_string($thousands)) {
                $end = str_replace($thousands, '', $values['end']);
            }

            if (is_string($decimal)) {
                $end = (float) str_replace($decimal, '.', $end);
            }

            return $collection->where($field, '<=', $end);
        }

        if (isset($values['start']) && isset($values['end'])) {
            $start = $values['start'];
            $end   = $values['end'];

            if (is_string($thousands)) {
                $start = str_replace($thousands, '', $values['start']);
                $end   = str_replace($thousands, '', $values['end']);
            }

            if (is_string($decimal)) {
                $start = str_replace($decimal, '.', $start);
                $end   = str_replace($decimal, '.', $end);
            }

            return $collection->whereBetween($field, [$start, $end]);
        }

        return $collection;
    }
}
