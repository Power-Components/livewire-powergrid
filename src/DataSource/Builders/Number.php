<?php

namespace PowerComponents\LivewirePowerGrid\DataSource\Builders;

use Closure;
use Illuminate\Database\Eloquent\{Builder, Model};
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Collection;

class Number extends BuilderBase
{
    /**
     * @param  int|array{start?: string|int|float, end?: string|int|float}|string|null  $values
     * @param  Builder<Model>|QueryBuilder  $builder
     */
    public function builder(Builder|QueryBuilder $builder, string $field, int|array|string|null $values): void
    {
        if ($closure = data_get($this->filterBase, 'builder')) {
            /** @var Closure $closure */
            $closure($builder, $values);

            return;
        }

        /** @var array{start?: string|int|float, end?: string|int|float} $values */
        $start = $this->parseNumber($values['start'] ?? null);
        $end = $this->parseNumber($values['end'] ?? null);

        if (! is_null($start) && is_null($end)) {
            $builder->where($field, '>=', $start);
        } elseif (is_null($start) && ! is_null($end)) {
            $builder->where($field, '<=', $end);
        } elseif (! is_null($start) && ! is_null($end)) {
            $builder->whereBetween($field, [$start, $end]);
        }
    }

    /**
     * @param  int|array{start?: string|int|float, end?: string|int|float}|string|null  $values
     * @param  Collection<int, mixed>  $collection
     * @return Collection<int, mixed>
     */
    public function collection(Collection $collection, string $field, int|array|string|null $values): Collection
    {
        if ($closure = data_get($this->filterBase, 'collection')) {
            /** @var Closure $closure */
            return $closure($collection, $values);
        }

        /** @var array{start?: string|int|float, end?: string|int|float} $values */
        $start = $this->parseNumber($values['start'] ?? null);
        $end = $this->parseNumber($values['end'] ?? null);

        if (! is_null($start) && is_null($end)) {
            return $collection->where($field, '>=', $start);
        }

        if (is_null($start) && ! is_null($end)) {
            return $collection->where($field, '<=', $end);
        }

        if (! is_null($start) && ! is_null($end)) {
            return $collection->whereBetween($field, [$start, $end]);
        }

        return $collection;
    }

    /**
     * Normalize a filter value into a float, honoring the configured thousands/decimal separators.
     * Returns null when no value was provided (so the corresponding bound is skipped).
     */
    private function parseNumber(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_string($value)) {
            $thousands = data_get($this->filterBase, 'thousands');
            $decimal = data_get($this->filterBase, 'decimal');

            if (is_string($thousands)) {
                $value = str_replace($thousands, '', $value);
            }

            if (is_string($decimal)) {
                $value = str_replace($decimal, '.', $value);
            }
        }

        return is_numeric($value) ? (float) $value : null;
    }
}
