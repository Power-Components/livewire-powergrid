<?php

namespace PowerComponents\LivewirePowerGrid\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\{Collection, Str};

class FilterEnumSelect extends FilterSelect
{
    use WithFilterBase;

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

    public static function builder(Builder $query, string $field, int|array|string|null $values): void
    {
        if (is_array($values)) {
            $field  = $field . '.' . key($values);
            $values = $values[key($values)];
        }

        /** @var Builder $query */
        if (filled($values)) {
            $query->where($field, $values);
        }
    }

    public static function collection(Collection $builder, string $field, int|array|string|null $values): Collection
    {
        if (filled($values)) {
            return $builder->where($field, $values);
        }

        return $builder;
    }
}
