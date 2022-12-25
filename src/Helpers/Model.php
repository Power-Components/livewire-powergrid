<?php

namespace PowerComponents\LivewirePowerGrid\Helpers;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\{Cache,Schema};
use Illuminate\Support\Str;
use PowerComponents\LivewirePowerGrid\Column;

class Model
{
    use InputOperators;

    private array $columns;

    private string $search;

    private array $relationSearch;

    private array $filters;

    private array $inputRangeConfig;

    private array $searchMorphs;

    public function __construct(private Builder $query)
    {
    }

    public static function query(Builder $query): Model
    {
        return new Model($query);
    }

    public function setColumns(array $columns): Model
    {
        $this->columns = $columns;

        return $this;
    }

    public function setInputRangeConfig(array $config): Model
    {
        $this->inputRangeConfig = $config;

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

    public function setSearchMorphs(array $array): Model
    {
        $this->searchMorphs = $array;

        return $this;
    }

    public function setInputTextOperators(array $operators): Model
    {
        $this->inputTextOperators = $operators;

        return $this;
    }

    public function filter(): Builder
    {
        foreach ($this->filters as $key => $type) {
            $this->query->where(function ($query) use ($key, $type) {
                foreach ($type as $field => $value) {
                    match ($key) {
                        'date_picker'         => $this->filterDatePicker($query, $field, $value),
                        'multi_select'        => $this->filterMultiSelect($query, $field, $value),
                        'select'              => $this->filterSelect($query, $field, $value),
                        'boolean'             => $this->filterBoolean($query, $field, $value),
                        'input_text_contains' => $this->filterInputTextContains($query, $field, $value),
                        'number'              => $this->filterNumber($query, $field, $value),
                        'input_text'          => $this->filterInputText($query, $field, $value),
                        default               => null
                    };
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

    public function filterMultiSelect(Builder $query, string $field, array $values): void
    {
        $empty = false;

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

    public function filterSelect(Builder $query, string $field, string|array|null $values): void
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

    public function filterBoolean(Builder $query, string $field, string|array|null $value): void
    {
        if (is_null($value)) {
            $value = 'all';
        }

        if (is_array($value)) {
            $field = $field . '.' . key($value);
            $value = $value[key($value)];
        }

        /** @var Builder $query */
        if ($value != 'all') {
            $value = ($value == 'true' || $value == '1');
            $query->where($field, '=', $value);
        }
    }

    public function filterInputTextContains(Builder $query, string $field, ?string $value): void
    {
        $query->where($field, SqlSupport::like(), '%' . $value . '%');
    }

    public function filterInputText(Builder $query, string $field, string|array|null $value): void
    {
        if (is_array($value) && blank($this->searchMorphs)) {
            $field = $field . '.' . key($value);
            $value = $value[key($value)];
        }

        $textFieldOperator = $this->validateInputTextOptions($this->filters, $field);

        $matchOperatorQuery = function ($textFieldOperator, $query, $field, $value) {
            match ($textFieldOperator) {
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
        };

        /**
        public function searchMorphs(): array
        {
            // https://laravel.com/docs/9.x/eloquent-relationships#querying-morph-to-relationships
            return [
                'record', // table
                'owner', // morph relationship
                [
                    \App\Models\Gym::class
                ]
            ];
        }**/

        if (filled($this->searchMorphs)) {
            $table        = $this->searchMorphs[0];
            $relationship = $this->searchMorphs[1];
            $types        = $this->searchMorphs[2];

            $query->whereHasMorph(
                $relationship,
                $types,
                fn (Builder $query) => $query->whereHas(
                    $table,
                    fn (Builder $query) => $matchOperatorQuery(
                        $textFieldOperator,
                        $query,
                        $field,
                        $value
                    )
                )
            );

            return;
        }

        $matchOperatorQuery($textFieldOperator, $query, $field, $value);
    }

    /**
     * @param string[] $value
     */
    public function filterNumber(Builder $query, string $field, array $value): void
    {
        if (isset($value['start']) && !isset($value['end'])) {
            $start = $value['start'];

            if (isset($this->inputRangeConfig[$field])) {
                $start = str_replace($this->inputRangeConfig[$field]['thousands'], '', $value['start']);
                $start = (float) str_replace($this->inputRangeConfig[$field]['decimal'], '.', $start);
            }

            $query->where($field, '>=', $start);
        }

        if (!isset($value['start']) && isset($value['end'])) {
            $end = $value['end'];

            if (isset($this->inputRangeConfig[$field])) {
                $end = str_replace($this->inputRangeConfig[$field]['thousands'], '', $value['end']);
                $end = (float) str_replace($this->inputRangeConfig[$field]['decimal'], '.', $end);
            }

            $query->where($field, '<=', $end);
        }

        if (isset($value['start']) && isset($value['end'])) {
            $start = $value['start'];
            $end   = $value['end'];

            if (isset($this->inputRangeConfig[$field])) {
                $start = str_replace($this->inputRangeConfig[$field]['thousands'], '', $value['start']);
                $start = str_replace($this->inputRangeConfig[$field]['decimal'], '.', $start);

                $end = str_replace($this->inputRangeConfig[$field]['thousands'], '', $value['end']);
                $end = str_replace($this->inputRangeConfig[$field]['decimal'], '.', $end);
            }

            $query->whereBetween($field, [$start, $end]);
        }
    }

    private function getColumnList(string $modelTable): array
    {
        try {
            return (array) Cache::remember('powergrid_columns_in_' . $modelTable, 600, fn () => Schema::getColumnListing($modelTable));
        } catch (\Exception) {
            return Schema::getColumnListing($modelTable);
        }
    }

    public function filterContains(): Model
    {
        if ($this->search != '') {
            $this->query = $this->query->where(function (Builder $query) {
                $modelTable = $query->getModel()->getTable();

                $columnList = $this->getColumnList($modelTable);

                /** @var Column $column */
                foreach ($this->columns as $column) {
                    $searchable = strval(data_get($column, 'searchable'));
                    $table      = $modelTable;
                    $field      = strval(data_get($column, 'dataField')) ?: strval(data_get($column, 'field'));

                    if ($searchable && $field) {
                        if (str_contains($field, '.')) {
                            $explodeField = Str::of($field)->explode('.');
                            /** @var string $table */
                            $table = $explodeField->get(0);
                            /** @var string $field */
                            $field = $explodeField->get(1);
                        }

                        $hasColumn = in_array($field, $columnList, true);

                        if ($hasColumn) {
                            $query->orWhere($table . '.' . $field, SqlSupport::like($query), '%' . $this->search . '%');
                        }

                        if ($sqlRaw = strval(data_get($column, 'searchableRaw'))) {
                            $query->orWhereRaw($sqlRaw . ' ' . SqlSupport::like($query) . ' \'%' . $this->search . '%\'');
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
                            $this->query = $this->query->orWhereHas(
                                $table . '.' . $nestedTable,
                                fn (Builder $query) => $query->where($nestedColumn, SqlSupport::like($query), '%' . $this->search . '%')
                            );
                        }
                    }
                } else {
                    $this->query = $this->query->orWhereHas(
                        $table,
                        fn (Builder $query) => $query->where($column, SqlSupport::like($query), '%' . $this->search . '%')
                    );
                }
            }
        }
    }
}
