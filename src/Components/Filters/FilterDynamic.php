<?php

namespace PowerComponents\LivewirePowerGrid\Components\Filters;

class FilterDynamic extends FilterBase
{
    public string $key = 'dynamic';

    public string $component = '';

    /** @var array<string, mixed> */
    public array $attributes = [];

    public string $baseClass = '';

    /** @param  array<string, mixed>  $attributes */
    public function attributes(array $attributes): FilterDynamic
    {
        $this->attributes = $attributes;

        return $this;
    }

    public function baseClass(string $attrClass): FilterDynamic
    {
        $this->baseClass = $attrClass;

        return $this;
    }
}
