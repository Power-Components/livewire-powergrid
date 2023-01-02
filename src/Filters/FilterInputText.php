<?php

namespace PowerComponents\LivewirePowerGrid\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\{Collection, Str};
use PowerComponents\LivewirePowerGrid\Helpers\SqlSupport;

class FilterInputText implements FilterBaseInterface
{
    use WithFilterBase;

    public array $operators = [];

    private static array $inputTextOptions = [
        'contains',
        'contains_not',
        'is',
        'is_not',
        'starts_with',
        'ends_with',
        'is_empty',
        'is_not_empty',
        'is_null',
        'is_not_null',
        'is_blank',
        'is_not_blank',
    ];

    public function operators(array $value = []): FilterInputText
    {
//        if (!in_array('contains', $value)) {
//            $value[] = 'contains';
//        }

        $this->operators = $value;

        return $this;
    }

    public static function builder(Builder $query, string $field, int|array|string|null $values): void
    {
        /** @var array $values */
        $value    = $values['value'];
        $selected = $values['selected'];

        if (is_array($value)) {
            $field = $field . '.' . key($value);
            $value = $value[key($value)];
        }

        match ($selected) {
            'is'           => $query->where($field, '=', $value),
            'is_not'       => $query->where($field, '!=', $value),
            'starts_with'  => $query->where($field, SqlSupport::like($query), $value . '%'),
            'ends_with'    => $query->where($field, SqlSupport::like($query), '%' . $value),
            'contains_not' => $query->where($field, 'NOT ' . SqlSupport::like($query), '%' . $value . '%'),
            'is_empty'     => $query->where($field, '=', '')->orWhereNull($field),
            'is_not_empty' => $query->where($field, '!=', '')->whereNotNull($field),
            'is_null'      => $query->whereNull($field),
            'is_not_null'  => $query->whereNotNull($field),
            'is_blank'     => $query->where($field, '=', ''),
            'is_not_blank' => $query->where($field, '!=', '')->orWhereNull($field),
            default        => $query->where($field, SqlSupport::like($query), '%' . $value . '%'),
        };
    }

    public static function collection(Collection $builder, string $field, int|array|string|null $values): Collection
    {
        /** @var array $values */
        $value    = $values['value'];
        $selected = $values['selected'];

        return match ($selected) {
            'is'           => $builder->where($field, '=', $value),
            'is_not'       => $builder->where($field, '!=', $value),
            'starts_with'  => $builder->filter(function ($row) use ($field, $value) {
                $row = (object) $row;

                return Str::startsWith(Str::lower($row->{$field}), Str::lower($value));
            }),
            'ends_with'    => $builder->filter(function ($row) use ($field, $value) {
                $row = (object) $row;

                return Str::endsWith(Str::lower($row->{$field}), Str::lower($value));
            }),
            'contains_not' => $builder->filter(function ($row) use ($field, $value) {
                $row = (object) $row;

                return !Str::Contains(Str::lower($row->{$field}), Str::lower($value));
            }),
            'is_empty'     => $builder->filter(function ($row) use ($field) {
                $row = (object) $row;

                return $row->{$field} == '' || is_null($row->{$field});
            }),
            'is_not_empty' => $builder->filter(function ($row) use ($field) {
                $row = (object) $row;

                return $row->{$field} !== '' && $row->{$field} !== null;
            }),
            'is_null'      => $builder->whereNull($field),
            'is_not_null'  => $builder->filter(function ($row) use ($field) {
                $row = (object) $row;

                return $row->{$field} !== '' && !is_null($row->{$field});
            }),
            'is_blank'     => $builder->whereNotNull($field)->where($field, '=', ''),
            'is_not_blank' => $builder->filter(function ($row) use ($field) {
                $row = (object) $row;

                return $row->{$field} != '' || is_null($row->{$field});
            }),
            default        => $builder->filter(function ($row) use ($field, $value) {
                $row = (object) $row;

                return false !== stristr($row->{$field}, strtolower($value));
            }),
        };
    }

    public static function getInputTextOperators(): array
    {
        return self::$inputTextOptions;
    }
}
