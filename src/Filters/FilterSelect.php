<?php

namespace PowerComponents\LivewirePowerGrid\Filters;

use Illuminate\Support\Collection;

class FilterSelect extends FilterBase
{
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
}
