<?php

namespace PowerComponents\LivewirePowerGrid\Helpers;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Services\Contracts\ModelFilterInterface;

class Model implements ModelFilterInterface
{
    private Builder $query;

    private array $columns;

    private string $search;

    private array $relationSearch;

    private array $filters;

    public function __construct(Builder $query)
    {
        $this->query = $query;
    }

    /**
     * @param Builder $query
     */
    public static function query($query):Model
    {
        return new Model($query);
    }

    public function setColumns(array $columns): Model
    {
        $this->columns = $columns;

        return $this;
    }

    public function setSearch(string $search): Model
    {
        $this->search = $search;

        return $this;
    }

    public function setFilters(array $filters): Model
    {
        $this->filters = $filters;

        return $this;
    }

    public function setRelationSearch(array $relations): Model
    {
        $this->relationSearch = $relations;

        return $this;
    }

    public function filter(): Builder
    {
        foreach ($this->filters as $key => $type) {
            $this->query->where(function ($query) use ($key, $type) {
                foreach ($type as $field => $value) {
                    switch ($key) {
                        case 'date_picker':
                            $this->filterDatePicker($query, $field, $value);

                            break;
                        case 'multi_select':
                            $this->filterMultiSelect($query, $field, $value);

                            break;
                        case 'select':
                            $this->filterSelect($query, $field, $value);

                            break;
                        case 'boolean':
                            $this->filterBoolean($query, $field, $value);

                            break;
                        case 'input_text':
                            $this->filterInputText($query, $field, $value);

                            break;
                        case 'number':
                            $this->filterNumber($query, $field, $value);

                            break;
                    }
                }
            });
        }

        return $this->query;
    }

    public function filterDatePicker(Builder $query, string $field, array $value): void
    {
        if (isset($value[0]) && isset($value[1])) {
            $query->whereBetween($field, [Carbon::parse($value[0]), Carbon::parse($value[1])]);
        }
    }

    /**
     * @param array $value
     */
    public function filterMultiSelect(Builder $query, string $field, array $value): void
    {
        $empty = false;

        if (!(array_key_exists('id', $value) && $value['id'] == $field)) {
            $field       = $field . '.' . array_key_first($value);
            $value       = $value[array_key_first($value)];
            $value['id'] = $field;
        }

        /** @var array $values */
        $values = collect($value)->get('values');

        if (is_array($values) && count($values) === 0) {
            return;
        }

        foreach ($values as $value) {
            if ($value === '') {
                $empty = true;
            }
        }
        if (!$empty) {
            $query->whereIn($field, $values);
        }
    }

    /**
     * @param string|array $value
     */
    public function filterSelect(Builder $query, string $field, $value): void
    {
        if (is_array($value)) {
            $field = $field . '.' . key($value);
            $value = $value[key($value)];
        }

        /** @var Builder $query */
        if (filled($value)) {
            $query->where($field, $value);
        }
    }

    /**
     * @param string|array $value
     */
    public function filterBoolean(Builder $query, string $field, $value): void
    {
        if (is_array($value)) {
            $field = $field . '.' . key($value);
            $value = $value[key($value)];
        }

        /** @var Builder $query */
        if ($value != 'all') {
            $value = ($value == 'true');
            $query->where($field, '=', $value);
        }
    }

    /**
     * @param string|array $value
     */
    public function filterInputText(Builder $query, string $field, $value): void
    {
        if (is_array($value)) {
            $field = $field . '.' . key($value);
            $value = $value[key($value)];
        }

        $textFieldOperator = (validateInputTextOptions($this->filters, $field) ? strtolower($this->filters['input_text_options'][$field]) : 'contains');
        switch ($textFieldOperator) {
            case 'is':
                $query->where($field, '=', $value);

                break;
            case 'is_not':
                $query->where($field, '!=', $value);

                break;
            case 'starts_with':
                $query->where($field, SqlSupport::like(), $value . '%');

                break;
            case 'ends_with':
                $query->where($field, SqlSupport::like(), '%' . $value);

                break;
            case 'contains':
                $query->where($field, SqlSupport::like(), '%' . $value . '%');

                break;
            case 'contains_not':
                $query->where($field, 'NOT ' . SqlSupport::like(), '%' . $value . '%');

                break;
            case 'is_empty':
                $query->where($field, '=', '')->orWhereNull($field);

                break;
            case 'is_not_empty':
                $query->where($field, '!=', '')->whereNotNull($field);

                break;
            case 'is_null':
                $query->whereNull($field);

                break;
            case 'is_not_null':
                $query->whereNotNull($field);

                break;
            case 'is_blank':
                $query->where($field, '=', '');

                break;
            case 'is_not_blank':
                $query->where($field, '!=', '')->orWhereNull($field);

                break;
        }
    }

    /**
     * @param string[] $value
     */
    public function filterNumber(Builder $query, string $field, array $value): void
    {
        if (isset($value['start']) && !isset($value['end'])) {
            $start = str_replace($value['thousands'], '', $value['start']);
            $start = (float) str_replace($value['decimal'], '.', $start);

            $query->where($field, '>=', $start);
        }
        if (!isset($value['start']) && isset($value['end'])) {
            $end = str_replace($value['thousands'], '', $value['end']);
            $end = (float) str_replace($value['decimal'], '.', $end);

            $query->where($field, '<=', $end);
        }
        if (isset($value['start']) && isset($value['end'])) {
            $start = str_replace($value['thousands'], '', $value['start']);
            $start = str_replace($value['decimal'], '.', $start);

            $end = str_replace($value['thousands'], '', $value['end']);
            $end = str_replace($value['decimal'], '.', $end);

            $query->whereBetween($field, [$start, $end]);
        }
    }

    public function filterContains(): Model
    {
        if ($this->search != '') {
            $this->query = $this->query->where(function (Builder $query) {
                $table = $query->getModel()->getTable();

                /** @var Column $column */
                foreach ($this->columns as $column) {
                    /** @var string $searchable */
                    $searchable = data_get($column, 'searchable');
                    /** @var string $field */
                    $field      = data_get($column, 'dataField') ?: data_get($column, 'field');

                    if ($searchable && $field) {
                        if (str_contains($field, '.')) {
                            $explodeField = Str::of($field)->explode('.');
                            /** @var string $table */
                            $table        = $explodeField->get(0);
                            /** @var string $field */
                            $field        = $explodeField->get(1);
                        }

                        $hasColumn = Schema::hasColumn($table, $field);

                        if ($hasColumn) {
                            $query->orWhere($table . '.' . $field, SqlSupport::like(), '%' . $this->search . '%');
                        }
                    }
                }

                return $query;
            });

            if (count($this->relationSearch)) {
                $this->filterRelation();
            }
        }

        return $this;
    }

    private function filterRelation(): void
    {
        foreach ($this->relationSearch as $table => $relation) {
            if (!is_array($relation)) {
                return;
            }

            foreach ($relation as $nestedTable => $column) {
                if (is_array($column)) {
                    /** @var Builder $query */
                    $query = $this->query->getRelation($table);

                    if ($query->getRelation($nestedTable) != '') {
                        foreach ($column as $nestedColumn) {
                            $this->query = $this->query->orWhereHas($table . '.' . $nestedTable, function (Builder $query) use ($nestedColumn) {
                                $query->where($nestedColumn, SqlSupport::like(), '%' . $this->search . '%');
                            });
                        }
                    }
                } else {
                    $this->query = $this->query->orWhereHas($table, function (Builder $query) use ($column) {
                        $query->where($column, SqlSupport::like(), '%' . $this->search . '%');
                    });
                }
            }
        }
    }
}
