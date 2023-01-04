<?php

namespace PowerComponents\LivewirePowerGrid\Filters;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class FilterNumber implements FilterBaseInterface
{
    use WithFilterBase;

    public string $thousands = '';

    public string $decimal = '';

    public function thousands(string $thousands): FilterNumber
    {
        $this->thousands = $thousands;

        return $this;
    }

    public function decimal(string $decimal): FilterNumber
    {
        $this->decimal = $decimal;

        return $this;
    }

    public static function builder(Builder $query, string $field, int|array|string|null $values): void
    {
        /** @var array $values */

        if (isset($values['start']) && !isset($values['end'])) {
            $start = $values['start'];

            if (isset($values['thousands'])) {
                $start = str_replace($values['thousands'], '', $start);
            }

            if (isset($values['decimal'])) {
                $start = str_replace($values['decimal'], '.', $start);
            }

            $query->where($field, '>=', $start);
        }

        if (!isset($values['start']) && isset($values['end'])) {
            $end = $values['end'];

            if (isset($values['decimal'])) {
                $end = str_replace($values['thousands'], '', $values['end']);
            }

            if (isset($values['decimal'])) {
                $end = (float) str_replace($values['decimal'], '.', $end);
            }

            $query->where($field, '<=', $end);
        }

        if (isset($values['start']) && isset($values['end'])) {
            $start = $values['start'];
            $end   = $values['end'];

            if (isset($values['thousands'])) {
                $start = str_replace($values['thousands'], '', $values['start']);
                $end   = str_replace($values['thousands'], '', $values['end']);
            }

            if (isset($values['decimal'])) {
                $start = str_replace($values['decimal'], '.', $start);
                $end   = str_replace($values['decimal'], '.', $end);
            }

            $query->whereBetween($field, [$start, $end]);
        }
    }

    public static function collection(Collection $builder, string $field, int|array|string|null $values): Collection
    {
        /** @var array $values */

        if (isset($values['start']) && !isset($values['end'])) {
            $start = $values['start'];

            if (isset($values['thousands'])) {
                $start = str_replace($values['thousands'], '', $values['start']);
            }

            if (isset($values['decimal'])) {
                $start = (float) str_replace($values['decimal'], '.', $start);
            }

            return $builder->where($field, '>=', $start);
        }

        if (!isset($values['start']) && isset($values['end'])) {
            $end = $values['end'];

            if (isset($values['thousands'])) {
                $end = str_replace($values['thousands'], '', $values['end']);
            }

            if (isset($values['decimal'])) {
                $end = (float) str_replace($values['decimal'], '.', $end);
            }

            return $builder->where($field, '<=', $end);
        }

        if (isset($values['start']) && isset($values['end'])) {
            $start = $values['start'];
            $end   = $values['end'];

            if (isset($values['thousands'])) {
                $start = str_replace($values['thousands'], '', $values['start']);
                $end   = str_replace($values['thousands'], '', $values['end']);
            }

            if (isset($values['decimal'])) {
                $start = str_replace($values['decimal'], '.', $start);
                $end   = str_replace($values['decimal'], '.', $end);
            }

            return $builder->whereBetween($field, [$start, $end]);
        }

        return $builder;
    }
}
