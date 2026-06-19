<?php

namespace PowerComponents\LivewirePowerGrid\Components\Filters;

class FilterMultiSelectAsync extends FilterBase
{
    public string $key = 'multi_select';

    public string $url = '';

    public string $method = 'get';

    public string $optionValue = '';

    public string $optionLabel = '';

    /** @var array<string, mixed> */
    public array $parameters = [];

    public function url(string $url): FilterMultiSelectAsync
    {
        $this->url = $url;

        return $this;
    }

    public function method(string $method = 'get'): FilterMultiSelectAsync
    {
        $this->method = $method;

        return $this;
    }

    /** @param  array<string, mixed>  $parameters */
    public function parameters(array $parameters): FilterMultiSelectAsync
    {
        $this->parameters = $parameters;

        return $this;
    }

    public function optionValue(string $value): FilterMultiSelectAsync
    {
        $this->optionValue = $value;

        return $this;
    }

    public function optionLabel(string $label): FilterMultiSelectAsync
    {
        $this->optionLabel = $label;

        return $this;
    }
}
