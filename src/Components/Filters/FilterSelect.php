<?php

namespace PowerComponents\LivewirePowerGrid\Components\Filters;

use Illuminate\Support\{Collection};
use Illuminate\View\ComponentAttributeBag;

class FilterSelect extends FilterBase
{
    public array|Collection|\Closure $dataSource;

    public string $optionValue = '';

    public string $optionLabel = '';

    public array $depends = [];

    public function depends(array $fields): FilterSelect
    {
        $this->depends = $fields;

        return $this;
    }

    public function dataSource(Collection|array|\Closure $collection): FilterSelect
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

    public static function getWireAttributes(string $field, string $title): array
    {
        return collect()
            ->put('selectAttributes', new ComponentAttributeBag([
                'wire:model'                => 'filters.select.' . $field,
                'wire:input.debounce.600ms' => 'filterSelect(\'' . $field . '\', \'' . $title . '\')',
            ]))
            ->toArray();
    }
}
