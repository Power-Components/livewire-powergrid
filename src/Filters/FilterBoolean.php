<?php

namespace PowerComponents\LivewirePowerGrid\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class FilterBoolean implements FilterBaseInterface
{
    use WithFilterBase;

    public string $trueLabel = 'Yes';

    public string $falseLabel = 'No';

    public function trueLabel(string $trueLabel): FilterBoolean
    {
        $this->trueLabel = $trueLabel;

        return $this;
    }

    public function falseLabel(string $falseLabel): FilterBoolean
    {
        $this->falseLabel = $falseLabel;

        return $this;
    }

    public static function builder(Builder $query, string $field, int|array|string|null $values): void
    {
        if (is_null($values)) {
            $values = 'all';
        }

        if (is_array($values)) {
            $field  = $field . '.' . key($values);
            $values = $values[key($values)];
        }

        /** @var Builder $query */
        if ($values != 'all') {
            $values = ($values == 'true' || $values == '1');
            $query->where($field, '=', $values);
        }
    }

    public static function collection(Collection $builder, string $field, int|array|string|null $values): Collection
    {
        if (is_null($values)) {
            $values = 'all';
        }

        if ($values != 'all') {
            $values = ($values == 'true');

            return $builder->where($field, '=', $values);
        }

        return $builder;
    }
}
