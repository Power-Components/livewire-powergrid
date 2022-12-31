<?php

namespace PowerComponents\LivewirePowerGrid\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class FilterSelect implements FilterBaseInterface
{
    use WithFilterBase;

    public array|Collection $dataSource;

    public string $optionValue = '';

    public string $optionLabel = '';

    public function dataSource(Collection|array $collection): FilterSelect
    {
        $this->dataSource = $collection;

        return $this;
    }

    public function optionValue(string $value): FilterSelect
    {
        $this->optionValue = $value;

        return $this;
    }

    public function optionLabel(string $value): FilterSelect
    {
        $this->optionLabel = $value;

        return $this;
    }

    public static function builder(Builder $query, string $field, int|array|string|null $values): void
    {
        if (is_array($values)) {
            $field  = $field . '.' . key($values);
            $values = $values[key($values)];
        }

        /** @var Builder $query */
        if (filled($values)) {
            $query->where($field, $values);
        }
    }

    public static function collection(Collection $builder, string $field, int|array|string|null $values): Collection
    {
        if (filled($values)) {
            return $builder->where($field, $values);
        }

        return $builder;
    }
}
