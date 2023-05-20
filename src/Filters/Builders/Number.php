<?php

namespace PowerComponents\LivewirePowerGrid\Filters\Builders;

use Illuminate\Database\Eloquent\{Builder, Builder as EloquentBuilder};
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Collection;

class Number extends BuilderBase
{
    public function builder(EloquentBuilder|QueryBuilder $builder, string $field, int|array|string|null $values): void
    {
        if (data_get($this->filterBase, 'builder')) {
            /** @var \Closure $closure */
            $closure = data_get($this->filterBase, 'builder');

            $closure($builder, $values);

            return;
        }

        /** @var array $values */
        if (isset($values['start']) && !isset($values['end'])) {
            $start = $values['start'];

            if (isset($values['thousands'])) {
                $start = str_replace($values['thousands'], '', $start);
            }

            if (isset($values['decimal'])) {
                $start = str_replace($values['decimal'], '.', $start);
            }

            $builder->where($field, '>=', $start);
        }

        if (!isset($values['start']) && isset($values['end'])) {
            $end = $values['end'];

            if (isset($values['decimal'])) {
                $end = str_replace($values['thousands'], '', $values['end']);
            }

            if (isset($values['decimal'])) {
                $end = (float) str_replace($values['decimal'], '.', $end);
            }

            $builder->where($field, '<=', $end);
        }

        if (isset($values['start']) && isset($values['end'])) {
            $start = $values['start'];
            $end   = $values['end'];

            if (isset($values['thousands'])) {
                $start = str_replace($values['thousands'], '', $values['start']);
                $end   = str_replace($values['thousands'], '', $values['end']);
            }

            if (isset($values['decimal'])) {
                $start = str_replace($values['decimal'], '.', $start);
                $end   = str_replace($values['decimal'], '.', $end);
            }

            $builder->whereBetween($field, [$start, $end]);
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
        if (isset($values['start']) && !isset($values['end'])) {
            $start = $values['start'];

            if (isset($values['thousands'])) {
                $start = str_replace($values['thousands'], '', $values['start']);
            }

            if (isset($values['decimal'])) {
                $start = (float) str_replace($values['decimal'], '.', $start);
            }

            return $collection->where($field, '>=', $start);
        }

        if (!isset($values['start']) && isset($values['end'])) {
            $end = $values['end'];

            if (isset($values['thousands'])) {
                $end = str_replace($values['thousands'], '', $values['end']);
            }

            if (isset($values['decimal'])) {
                $end = (float) str_replace($values['decimal'], '.', $end);
            }

            return $collection->where($field, '<=', $end);
        }

        if (isset($values['start']) && isset($values['end'])) {
            $start = $values['start'];
            $end   = $values['end'];

            if (isset($values['thousands'])) {
                $start = str_replace($values['thousands'], '', $values['start']);
                $end   = str_replace($values['thousands'], '', $values['end']);
            }

            if (isset($values['decimal'])) {
                $start = str_replace($values['decimal'], '.', $start);
                $end   = str_replace($values['decimal'], '.', $end);
            }

            return $collection->whereBetween($field, [$start, $end]);
        }

        return $collection;
    }
}
