<?php

namespace PowerComponents\LivewirePowerGrid\Components\Filters;

use Illuminate\Support\Collection;

class FilterMultiSelect extends FilterBase
{
    public string $key = 'multi_select';

    /** @var array<int, mixed>|Collection<int, mixed> */
    public array|Collection $dataSource;

    public string $optionValue = '';

    public string $optionLabel = '';

    /** @var array<string, mixed> */
    public array $params = [];

    /** @param  Collection<int, mixed>|array<int, mixed>  $collection */
    public function dataSource(Collection|array $collection): FilterMultiSelect
    {
        $this->dataSource = $collection;

        return $this;
    }

    public function optionValue(string $value): FilterMultiSelect
    {
        $this->optionValue = $value;

        return $this;
    }

    public function optionLabel(string $value): FilterMultiSelect
    {
        $this->optionLabel = $value;

        return $this;
    }

    /** @param  array<string, mixed>  $params */
    public function params(array $params): FilterMultiSelect
    {
        $this->params = $params;

        return $this;
    }
}
