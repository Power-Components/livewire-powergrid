<?php

namespace PowerComponents\LivewirePowerGrid\Filters;

use Illuminate\Support\Collection;
use PowerComponents\LivewirePowerGrid\Filters\Builders\WithMultiSelectBuilder;

class FilterMultiSelect implements FilterBaseInterface
{
    use WithFilterBase;
    use WithMultiSelectBuilder;

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
