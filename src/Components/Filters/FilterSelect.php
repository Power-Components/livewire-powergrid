<?php

namespace PowerComponents\LivewirePowerGrid\Components\Filters;

use Closure;
use Illuminate\Support\{Collection};
use PowerComponents\LivewirePowerGrid\FilterAttributes\Select;

class FilterSelect extends FilterBase
{
    public string $key = 'select';

    public array|Collection|Closure $dataSource;

    public string $optionValue = '';

    public string $optionLabel = '';

    public array $depends = [];

    public array $params = [];

    public string $computedDatasource = '';

    public function depends(array $fields): FilterSelect
    {
        $this->depends = $fields;

        return $this;
    }

    public function dataSource(Collection|array|Closure $collection): FilterSelect
    {
        $this->dataSource = $collection;

        return $this;
    }

    public function computedDatasource(string $computedDatasource): FilterSelect
    {
        $this->computedDatasource = $computedDatasource;

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
        $configAttributes = config('livewire-powergrid.filter_attributes.select', Select::class);

        /** @var callable $class */
        $class = new $configAttributes();

        return $class($field, $title);
    }

    public function params(array $params): FilterSelect
    {
        $this->params = $params;

        return $this;
    }
}
