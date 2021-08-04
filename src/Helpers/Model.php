<?php

namespace PowerComponents\LivewirePowerGrid\Helpers;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;
use PowerComponents\LivewirePowerGrid\Services\Contracts\FilterInterface;

class Model implements FilterInterface
{
    private Builder $query;

    private array $columns;

    private string $search;

    private array $relationSearch;

    private array $filters;

    /**
     * Model constructor.
     */
    public function __construct($query)
    {
        $this->query = $query;
    }

    public static function query($query): Model
    {
        return new static($query);
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

    /**
     * Validate if the given value is valid as an Input Option
     *
     * @param string $field Field to be checked
     * @return bool
     */
    private function validateInputTextOptions(string $field): bool
    {
        return isset($this->filters['input_text_options'][$field]) && in_array(
                strtolower($this->filters['input_text_options'][$field]),
                ['is', 'is_not', 'contains', 'contains_not', 'starts_with', 'ends_with']
            );
    }

    /**
     * @param $query
     * @param string $field
     * @param $value
     */
    public function filterDatePicker($query, string $field, $value)
    {
        if (isset($value[0]) && isset($value[1])) {
            $query->whereBetween($field, [Carbon::parse($value[0]), Carbon::parse($value[1])]);
        }
    }

    /**
     * @param Builder $query
     * @param string $field
     * @param $value
     */
    public function filterInputText(Builder $query, string $field, $value)
    {
        $textFieldOperator = ($this->validateInputTextOptions($field) ? strtolower($this->filters['input_text_options'][$field]) : 'contains');

        switch ($textFieldOperator) {
            case 'is' :
                $query->where($field, '=', $value);

                break;
            case 'is_not' :
                $query->where($field, '!=', $value);

                break;
            case 'starts_with' :
                $query->where($field, 'like', $value . '%');

                break;
            case 'ends_with' :
                $query->where($field, 'like', '%' . $value);

                break;
            case 'contains' :
                $query->where($field, 'like', '%' . $value . '%');

                break;
            case 'contains_not' :
                $query->where($field, 'not like', '%' . $value . '%');

                break;
        }
    }

    /**
     * @param $query
     * @param string $field
     * @param $value
     */
    public function filterBoolean($query, string $field, $value)
    {
        /** @var \Illuminate\Database\Eloquent\Builder $query */
        if ($value != "all") {
            $value = ($value == "true");
            $query->where($field, '=', $value);
        }
    }

    /**
     * @param $query
     * @param string $field
     * @param $value
     */
    public function filterSelect($query, string $field, $value)
    {
        /** @var \Illuminate\Database\Eloquent\Builder $query */
        if (filled($value)) {
            $query->where($field, $value);
        }
    }

    /**
     * @param $query
     * @param string $field
     * @param $value
     */
    public function filterMultiSelect($query, string $field, $value)
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
            $query->whereIn($field, $values);
        }
    }

    /**
     * @param $collection
     * @param string $field
     * @param $value
     */
    public function filterNumber($query, string $field, $value)
    {
        if (isset($value['start']) && !isset($value['end'])) {
            $start = str_replace($value['thousands'], '', $value['start']);
            $start = (float)str_replace($value['decimal'], '.', $start);

            $query->where($field, '>=', $start);
        }
        if (!isset($value['start']) && isset($value['end'])) {
            $end = str_replace($value['thousands'], '', $value['end']);
            $end = (float)str_replace($value['decimal'], '.', $end);

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
                foreach ($this->columns as $column) {
                    $hasColumn = Schema::hasColumn($query->getModel()->getTable(), $column->field);
                    if ($hasColumn) {
                        $query->orWhere($column->field, 'like', '%' . $this->search . '%');
                    }
                }

                return $query;
            });

            if (count($this->relationSearch)) {
                $this->makeRelation();
            }
        }

        return $this;
    }

    private function makeRelation(): void
    {
        foreach ($this->relationSearch as $table => $relation) {
            if (!is_array($relation)) {
                return;
            }

            if ($this->query->getRelation($table)) {
                foreach ($relation as $column) {

                    if (!Schema::hasColumn($this->query->getModel()->getTable(), $column)) {
                        return;
                    }

                    $this->query = $this->query->orWhereHas($table, function (Builder $query) use ($column) {
                        $query->where($column, 'like', '%' . $this->search . '%');
                    });
                }
            }
        }
    }
}
