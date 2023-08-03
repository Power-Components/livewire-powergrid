<?php

namespace PowerComponents\LivewirePowerGrid\Components\Filters;

use Illuminate\Support\{Collection};
use Illuminate\View\ComponentAttributeBag;

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

    public static function getWireAttributes(string $field, string $title): array
    {
        return collect()
            ->put('selectAttributes', new ComponentAttributeBag([
                'wire:model.debounce.500ms' => 'filters.select.' . $field,
                'wire:input.debounce.500ms' => 'filterSelect(\'' . $field . '\', \'' . $title . '\')',
            ]))
            ->toArray();
    }
}
