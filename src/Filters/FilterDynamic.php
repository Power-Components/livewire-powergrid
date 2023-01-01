<?php

namespace PowerComponents\LivewirePowerGrid\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class FilterDynamic implements FilterBaseInterface
{
    use WithFilterBase;

    public string $filterType;

    public string $component = '';

    public array $attributes = [];

    public string $baseClass = '';

    public function filterType(string $filterType): FilterDynamic
    {
        $this->filterType = $filterType;

        return $this;
    }

    public function component(string $value): FilterDynamic
    {
        $this->component = $value;

        return $this;
    }

    public function attributes(array $attributes): FilterDynamic
    {
        $this->attributes = $attributes;

        return $this;
    }

    public function baseClass(string $value): FilterDynamic
    {
        $this->baseClass = $value;

        return $this;
    }

    public static function builder(Builder $query, string $field, int|array|string|null $values): void
    {
    }

    public static function collection(Collection $builder, string $field, int|array|string|null $values): Collection
    {
        return $builder;
    }
}
