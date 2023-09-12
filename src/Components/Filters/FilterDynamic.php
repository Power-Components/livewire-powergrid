<?php

namespace PowerComponents\LivewirePowerGrid\Components\Filters;

class FilterDynamic extends FilterBase
{
    public string $component = '';

    public array $attributes = [];

    public string $baseClass = '';

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
