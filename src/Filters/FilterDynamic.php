<?php

namespace PowerComponents\LivewirePowerGrid\Filters;

class FilterDynamic extends FilterBase
{
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
}
