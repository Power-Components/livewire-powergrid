<?php

namespace PowerComponents\LivewirePowerGrid\Services\Contracts;

interface FilterInterface
{
    public static function filter($filters, $query);

    public static function filterDatePicker($collection, string $field, $value);

    public static function filterInputText($collection, string $field, $value, $filters);

    public static function filterBoolean($collection, string $field, $value);

    public static function filterSelect($collection, string $field, $value);

    public static function filterMultiSelect($collection, string $field, $value);

    public static function filterNumber($collection, string $field, $value);
}
