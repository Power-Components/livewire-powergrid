<?php

namespace PowerComponents\LivewirePowerGrid\Helpers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Services\Contracts\FilterInterface;

class Model implements FilterInterface
{
    public static function filter($filters, $query)
    {
        foreach ($filters as $key => $type) {
            $query->where(function ($query) use ($key, $type, $filters) {
                foreach ($type as $field => $value) {
                    switch ($key) {
                        case 'date_picker':
                            self::filterDatePicker($query, $field, $value);

                            break;
                        case 'multi_select':
                            self::filterMultiSelect($query, $field, $value);

                            break;
                        case 'select':
                            self::filterSelect($query, $field, $value);

                            break;
                        case 'boolean':
                            self::filterBoolean($query, $field, $value);

                            break;
                        case 'input_text':
                            self::filterInputText($query, $field, $value, $filters);

                            break;
                        case 'number':
                            self::filterNumber($query, $field, $value);

                            break;
                    }
                }

                return $query;
            });
        }
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
        return isset($filters['input_text_options'][$field]) && in_array(
            strtolower($filters['input_text_options'][$field]),
            ['is', 'is_not', 'contains', 'contains_not', 'starts_with', 'ends_with']
        );
    }

    /**
     * @param $collection
     * @param string $field
     * @param $value
     */
    public static function filterDatePicker($collection, string $field, $value)
    {
        if (isset($value[0]) && isset($value[1])) {
            $collection->whereBetween($field, [Carbon::parse($value[0]), Carbon::parse($value[1])]);
        }
    }

    /**
     * @param $query
     * @param string $field
     * @param $value
     * @param $filters
     */
    public static function filterInputText($query, string $field, $value, $filters)
    {
        $textFieldOperator = (self::validateInputTextOptions($field, $filters) ? strtolower($filters['input_text_options'][$field]) : 'contains');

        if ($textFieldOperator == 'is') {
            $query->where($field, '=', $value);
        }

        if ($textFieldOperator == 'is_not') {
            $query->where($field, '!=', $value);
        }

        if ($textFieldOperator == 'starts_with') {
            $query->where($field, 'like', $value . '%');
        }

        if ($textFieldOperator == 'ends_with') {
            $query->where($field, 'like', '%' . $value);
        }

        if ($textFieldOperator == 'contains') {
            $query->where($field, 'like', '%' . $value . '%');
        }

        if ($textFieldOperator == 'contains_not') {
            $query->where($field, 'not like', '%' . $value . '%');
        }
    }

    /**
     * @param $collection
     * @param string $field
     * @param $value
     */
    public static function filterBoolean($collection, string $field, $value)
    {
        if ($value != "all") {
            $value = ($value == "true");
            $collection->where($field, '=', $value);
        }
    }

    /**
     * @param $collection
     * @param string $field
     * @param $value
     */
    public static function filterSelect($collection, string $field, $value)
    {
        if (filled($value)) {
            $collection->where($field, $value);
        }
    }

    /**
     * @param $collection
     * @param string $field
     * @param $value
     */
    public static function filterMultiSelect($collection, string $field, $value)
    {
        $empty  = false;
        $values = collect($value)->get('values');
        if (count($values) === 0) {
            return;
        }
        foreach ($values as $value) {
            if ($value === '') {
                $empty = true;
            }
        }
        if (!$empty) {
            $collection->whereIn($field, $values);
        }
    }

    /**
     * @param $collection
     * @param string $field
     * @param $value
     */
    public static function filterNumber($collection, string $field, $value)
    {
        if (isset($value['start']) && !isset($value['end'])) {
            $start = str_replace($value['thousands'], '', $value['start']);
            $start = (float)str_replace($value['decimal'], '.', $start);

            $collection->where($field, '>=', $start);
        }
        if (!isset($value['start']) && isset($value['end'])) {
            $end = str_replace($value['thousands'], '', $value['end']);
            $end = (float)str_replace($value['decimal'], '.', $end);

            $collection->where($field, '<=', $end);
        }
        if (isset($value['start']) && isset($value['end'])) {
            $start = str_replace($value['thousands'], '', $value['start']);
            $start = str_replace($value['decimal'], '.', $start);

            $end = str_replace($value['thousands'], '', $value['end']);
            $end = str_replace($value['decimal'], '.', $end);

            $collection->whereBetween($field, [$start, $end]);
        }
    }

    public static function filterContains($query, array $columns, string $search, string $table)
    {
        if ($search != '') {
            $query->where(function ($query) use ($columns, $search, $table) {
                foreach ($columns as $column) {
                    $hasColumn = Schema::hasColumn($table, $column->field);
                    if ($hasColumn) {
                        $query->orWhere($column->field, 'like', '%' . $search . '%');
                    }
                }
            });
        }
    }
}
