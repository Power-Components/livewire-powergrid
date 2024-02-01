<?php

namespace PowerComponents\LivewirePowerGrid\Components\Filters;

use Illuminate\Support\Collection;

class FilterEnumSelect extends FilterBase
{
    public string $key = 'select';

    public array|Collection $dataSource;

    public string $optionValue = 'value';

    public string $optionLabel = '';

    public function dataSource(Collection|array $enumCases): FilterEnumSelect
    {
        $this->dataSource = $enumCases;

        return $this;
    }

    public function execute(): FilterEnumSelect
    {
        $optionLabel = 'value';

        $collection = collect($this->dataSource)->map(function ($case) use (&$optionLabel) {
            $option = (array) $case;

            if (method_exists($case, 'labelPowergridFilter')) {
                $option['name'] = $case->labelPowergridFilter();
                $optionLabel    = 'name';
            }

            return $option;
        });

        $this->dataSource($collection);

        $this->optionLabel($optionLabel);

        $this->optionValue($this->optionValue);

        return $this;
    }

    public function optionValue(string $value): FilterEnumSelect
    {
        $this->optionValue = $value;

        return $this;
    }

    public function optionLabel(string $value): FilterEnumSelect
    {
        $this->optionLabel = $value;

        return $this;
    }
}
