<?php

namespace PowerComponents\LivewirePowerGrid\Helpers;

use Illuminate\Container\Container;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection as BaseCollection;
use Illuminate\Support\Str;
use PowerComponents\LivewirePowerGrid\Services\Contracts\FilterInterface;

class Collection implements FilterInterface
{
    public static function paginate( BaseCollection $results, $pageSize ): LengthAwarePaginator
    {
        $page = Paginator::resolveCurrentPage('page');

        $total = $results->count();

        return self::paginator($results->forPage($page, $pageSize), $total, $pageSize, $page, [
            'path'     => Paginator::resolveCurrentPath(),
            'pageName' => 'page',
        ]);
    }

    /**
     * Create a new length-aware paginator instance.
     *
     * @param Collection $items
     * @param int $total
     * @param int $perPage
     * @param int $currentPage
     * @param array $options
     * @return LengthAwarePaginator
     */
    protected static function paginator( $items, $total, $perPage, $currentPage, $options ): LengthAwarePaginator
    {
        return Container::getInstance()->makeWith(LengthAwarePaginator::class, compact(
            'items', 'total', 'perPage', 'currentPage', 'options'
        ));
    }

    public static function search($model, string $search, $columns)
    {
        if (!empty($search)) {
            $model = $model->filter(function ($row) use ($columns, $search) {
                foreach ($columns as $column) {
                    $field = $column->field;
                    if (Str::contains(strtolower($row->$field), strtolower($search))) {
                        return false !== stristr($row->$field, strtolower($search));
                    }
                }
                return false;
            });
        }

        return $model;
    }

    public static function filter($filters, $query)
    {
        if (count($filters)) {
            foreach ($filters as $key => $type) {
                foreach ($type as $field => $value) {
                    if (filled($value)) {
                        switch ($key) {
                            case 'date_picker':
                                $query = self::filterDatePicker($query, $field, $value);

                                break;
                            case 'multi_select':
                                $query = self::filterMultiSelect($query, $field, $value);

                                break;
                            case 'select':
                                $query = self::filterSelect($query, $field, $value);

                                break;
                            case 'boolean':
                                $query = self::filterBoolean($query, $field, $value);

                                break;
                            case 'input_text':
                                $query = self::filterInputText($query, $field, $value, $filters);

                                break;
                            case 'number':
                                $query = self::filterNumber($query, $field, $value);

                                break;
                        }
                    }
                }
            }

            return $query;
        }

        return $query;
    }

    public static function filterDatePicker($collection, string $field, $value)
    {
        if (isset($value[0]) && isset($value[1])) {
            $collection = $collection->whereBetween($field, [Carbon::parse($value[0]), Carbon::parse($value[1])]);
        }

        return $collection;
    }

    /**
     * Validate if the given value is valid as an Input Option
     *
     * @param string $field Field to be checked
     * @param $filters
     * @return bool
     */
    private static function validateInputTextOptions(string $field, $filters): bool
    {
        return isset($filters['input_text_options'][$field]) && in_array(strtolower($filters['input_text_options'][$field]),
                ['is', 'is_not', 'contains', 'contains_not', 'starts_with', 'ends_with']);
    }

    public static function filterInputText($collection, string $field, $value, $filters)
    {
        $textFieldOperator = (self::validateInputTextOptions($field, $filters) ? strtolower($filters['input_text_options'][$field]) : 'contains');

        if ($textFieldOperator == 'is') {
            return $collection->where($field, '=', $value);
        }

        if ($textFieldOperator == 'is_not') {
            return $collection->where($field, '!=', $value);
        }

        if ($textFieldOperator == 'starts_with') {
            return $collection->filter(function ($row) use ($field, $value) {
                return Str::startsWith(Str::lower($row->$field), Str::lower($value));
            });
        }

        if ($textFieldOperator == 'ends_with') {
            return $collection->filter(function ($row) use ($field, $value) {
                return Str::endsWith(Str::lower($row->$field), Str::lower($value));
            });
        }

        if ($textFieldOperator == 'contains') {
            return $collection->filter(function ($row) use ($field, $value) {
                return false !== stristr($row->$field, strtolower($value));
            });
        }

        if ($textFieldOperator == 'contains_not') {
            return $collection->filter(function ($row) use ($field, $value) {
                return !Str::Contains(Str::lower($row->$field), Str::lower($value));
            });
        }

        return $collection;
    }

    public static function filterBoolean($collection, string $field, $value)
    {
        if ($value != "all") {
            $value = ($value == "true");

            return $collection->where($field, '=', $value);
        }

        return $collection;
    }

    public static function filterSelect($collection, string $field, $value)
    {
        if (filled($value)) {
            return $collection->where($field, $value);
        }
    }

    public static function filterMultiSelect($collection, string $field, $value)
    {
        $empty  = false;
        $values = collect($value)->get('values');
        if (count($values)) {
            foreach ($values as $value) {
                if ($value === '') {
                    $empty = true;
                }
            }
            if (!$empty) {
                $collection->whereIn($field, $values);
            }
        }

        return $collection;
    }

    public static function filterNumber($collection, string $field, $value)
    {
        if (isset($value['start']) && !isset($value['end'])) {
            $start = str_replace($value['thousands'], '', $value['start']);
            $start = (float)str_replace($value['decimal'], '.', $start);

            return $collection->where($field, '>=', $start);
        }
        if (!isset($value['start']) && isset($value['end'])) {
            $end = str_replace($value['thousands'], '', $value['end']);
            $end = (float)str_replace($value['decimal'], '.', $end);

            return $collection->where($field, '<=', $end);
        }
        if (isset($value['start']) && isset($value['end'])) {
            $start = str_replace($value['thousands'], '', $value['start']);
            $start = str_replace($value['decimal'], '.', $start);

            $end = str_replace($value['thousands'], '', $value['end']);
            $end = str_replace($value['decimal'], '.', $end);

            return $collection->whereBetween($field, [$start, $end]);
        }

        return $collection;
    }

    public static function filterContains($collection, array $columns, string $search)
    {

        if (!empty($search)) {
            return $collection->filter(function ($row) use ($columns, $search) {
                foreach ($columns as $column) {
                    $field = $column->field;
                    if (Str::contains(strtolower($row->{$field}), strtolower($search))) {
                        return false !== stristr($row->{$field}, strtolower($search));
                    }
                }

                return false;
            });
        }

        return $collection;
    }
}
