<?php

namespace PowerComponents\LivewirePowerGrid\Components\Filters;

use Illuminate\Support\Collection;

class FilterMultiSelect extends FilterBase
{
    public string $key = 'multi_select';

    public array|Collection $dataSource;

    public string $optionValue = '';

    public string $optionLabel = '';

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
}
