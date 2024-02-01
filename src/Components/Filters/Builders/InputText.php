<?php

namespace PowerComponents\LivewirePowerGrid\Components\Filters\Builders;

use Illuminate\Database\Eloquent\{Builder, Builder as EloquentBuilder};
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\{Collection, Str};
use PowerComponents\LivewirePowerGrid\DataSource\Support\Sql;

class InputText extends BuilderBase
{
    public function builder(EloquentBuilder|QueryBuilder $builder, string $field, int|array|string|null $values): void
    {
        if ($filterRelation = (array) data_get($this->filterBase, 'filterRelation')) {
            $relation = strval(data_get($filterRelation, 'relation'));
            $field    = strval(data_get($filterRelation, 'field'));

            $closure = $this->builderRelation($relation, $field);

            $closure($builder, $values);

            return;
        }

        if (data_get($this->filterBase, 'builder')) {
            /** @var \Closure $closure */
            $closure = data_get($this->filterBase, 'builder');

            $closure($builder, $values);

            return;
        }

        /** @var array $values */
        $value        = $values['value'];
        $selected     = $values['selected'];
        $searchMorphs = $values['searchMorphs'];

        if (is_array($value) && count($value) > 0 && blank($searchMorphs)) {
            $field = $field . '.' . key($value);
            $value = $value[key($value)];
        }

        $matchOperatorQuery = function (string $selected, EloquentBuilder|QueryBuilder $query, string $field, mixed $value) {
            match ($selected) {
                'is'           => $query->where($field, '=', $value),
                'is_not'       => $query->where($field, '!=', $value),
                'starts_with'  => $query->where($field, Sql::like($query), $value . '%'),
                'ends_with'    => $query->where($field, Sql::like($query), '%' . $value),
                'contains_not' => $query->where($field, 'NOT ' . Sql::like($query), '%' . $value . '%'),
                'is_empty'     => $query->where($field, '=', '')->orWhereNull($field),
                'is_not_empty' => $query->where($field, '!=', '')->whereNotNull($field),
                'is_null'      => $query->whereNull($field),
                'is_not_null'  => $query->whereNotNull($field),
                'is_blank'     => $query->where($field, '=', ''),
                'is_not_blank' => $query->where($field, '!=', '')->orWhereNull($field),
                default        => $query->where($field, Sql::like($query), '%' . $value . '%'),
            };
        };

        if (filled($searchMorphs) && $builder instanceof EloquentBuilder) {
            $table        = $searchMorphs[0];
            $relationship = $searchMorphs[1];
            $types        = $searchMorphs[2];

            $builder->whereHasMorph(
                $relationship,
                $types,
                fn (EloquentBuilder $query) => $query->whereHas(
                    $table,
                    fn (EloquentBuilder $query) => $matchOperatorQuery(
                        $selected,
                        $query,
                        $field,
                        $value
                    )
                )
            );

            return;
        }

        $matchOperatorQuery($selected, $builder, $field, $value);
    }

    public function collection(Collection $collection, string $field, int|array|string|null $values): Collection
    {
        if (data_get($this->filterBase, 'collection')) {
            /** @var \Closure $closure */
            $closure = data_get($this->filterBase, 'collection');

            return $closure($collection, $values);
        }

        /** @var array $values */
        $value    = $values['value'];
        $selected = $values['selected'];

        return match ($selected) {
            'is'          => $collection->where($field, '=', $value),
            'is_not'      => $collection->where($field, '!=', $value),
            'starts_with' => $collection->filter(function ($row) use ($field, $value) {
                $row = (object) $row;

                return Str::startsWith(Str::lower($row->{$field}), Str::lower($value));
            }),
            'ends_with' => $collection->filter(function ($row) use ($field, $value) {
                $row = (object) $row;

                return Str::endsWith(Str::lower($row->{$field}), Str::lower($value));
            }),
            'contains_not' => $collection->filter(function ($row) use ($field, $value) {
                $row = (object) $row;

                return !Str::Contains(Str::lower($row->{$field}), Str::lower($value));
            }),
            'is_empty' => $collection->filter(function ($row) use ($field) {
                $row = (object) $row;

                return $row->{$field} == '' || is_null($row->{$field});
            }),
            'is_not_empty' => $collection->filter(function ($row) use ($field) {
                $row = (object) $row;

                return $row->{$field} !== '' && $row->{$field} !== null;
            }),
            'is_null'     => $collection->whereNull($field),
            'is_not_null' => $collection->filter(function ($row) use ($field) {
                $row = (object) $row;

                return $row->{$field} !== '' && !is_null($row->{$field});
            }),
            'is_blank'     => $collection->whereNotNull($field)->where($field, '=', ''),
            'is_not_blank' => $collection->filter(function ($row) use ($field) {
                $row = (object) $row;

                return $row->{$field} != '' || is_null($row->{$field});
            }),
            default => $collection->filter(function ($row) use ($field, $value) {
                $row = (object) $row;

                return false !== stristr($row->{$field}, strtolower($value));
            }),
        };
    }

    private function builderRelation(string $relation, string $field): \Closure
    {
        return function (Builder $query, array $params) use ($relation, $field) {
            $value = $params['value'];

            match ($params['selected']) {
                'is'           => $query->whereRelation($relation, $field, '=', $value),
                'is_not'       => $query->whereRelation($relation, $field, '!=', $value),
                'starts_with'  => $query->whereRelation($relation, $field, Sql::like($query), $value . '%'),
                'ends_with'    => $query->whereRelation($relation, $field, Sql::like($query), '%' . $value),
                'contains_not' => $query->whereRelation($relation, $field, 'NOT ' . Sql::like($query), '%' . $value . '%'),
                'is_empty'     => $query->whereRelation($relation, function (Builder $query) use ($field) {
                    $query->where($field, '=', '')->orWhereNull($field);
                }),
                'is_not_empty' => $query->whereRelation($relation, function (Builder $query) use ($field) {
                    $query->where($field, '!=', '')->orWhereNotNull($field);
                }),
                'is_null' => $query->whereRelation($relation, function (Builder $query) use ($field) {
                    $query->whereNull($field);
                }),
                'is_not_null' => $query->whereRelation($relation, function (Builder $query) use ($field) {
                    $query->whereNotNull($field);
                }),
                'is_blank'     => $query->whereRelation($relation, $field, '=', ''),
                'is_not_blank' => $query->whereRelation($relation, function (Builder $query) use ($field) {
                    $query->where($field, '!=', '')->orWhereNull($field);
                }),
                default => $query->whereRelation($relation, $field, Sql::like($query), '%' . $value . '%'),
            };
        };
    }
}
