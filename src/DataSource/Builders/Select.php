<?php

namespace PowerComponents\LivewirePowerGrid\DataSource\Builders;

use Closure;
use Illuminate\Database\Eloquent\{Builder, Model};
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Collection;

class Select extends BuilderBase
{
    /**
     * @param  int|array<string, mixed>|string|null  $values
     * @param  Builder<Model>|QueryBuilder  $builder
     */
    public function builder(Builder|QueryBuilder $builder, string $field, int|array|string|null $values): void
    {
        if (data_get($this->filterBase, 'builder')) {
            /** @var Closure $closure */
            $closure = data_get($this->filterBase, 'builder');

            $closure($builder, $values);

            return;
        }

        if (is_array($values)) {
            [$field, $values] = self::appendNestedField($field, $values);
        }

        if (filled($values)) {
            $builder->where($field, $values);
        }
    }

    /**
     * @param  int|array<string, mixed>|string|null  $values
     * @param  Collection<int, mixed>  $collection
     * @return Collection<int, mixed>
     */
    public function collection(Collection $collection, string $field, int|array|string|null $values): Collection
    {
        if (data_get($this->filterBase, 'collection')) {
            /** @var Closure $closure */
            $closure = data_get($this->filterBase, 'collection');

            return $closure($collection, $values);
        }

        if (filled($values)) {
            return $collection->where($field, $values);
        }

        return $collection;
    }
}
