<?php

namespace PowerComponents\LivewirePowerGrid\Helpers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\{Cache,Schema};
use Illuminate\Support\Str;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Filters\{FilterBoolean,
    FilterDatePicker,
    FilterInputText,
    FilterMultiSelect,
    FilterNumber,
    FilterSelect};

class Model
{
    use InputOperators;

    private array $columns;

    private string $search;

    private array $relationSearch;

    private array $filters;

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

    public function filter(): Builder
    {
        foreach ($this->filters as $key => $type) {
            $this->query->where(function ($query) use ($key, $type) {
                foreach ($type as $field => $value) {
                    match ($key) {
                        'date_picker'  => FilterDatePicker::builder($query, $field, $value),
                        'multi_select' => FilterMultiSelect::builder($query, $field, $value),
                        'select'       => FilterSelect::builder($query, $field, $value),
                        'boolean'      => FilterBoolean::builder($query, $field, $value),
                        'number'       => FilterNumber::builder($query, $field, $value),
                        'input_text'   => FilterInputText::builder($query, $field, [
                            'selected'     => $this->validateInputTextOptions($this->filters, $field),
                            'value'        => $value,
                            'searchMorphs' => $this->searchMorphs,
                        ]),
                        default => null
                    };
                }
            });
        }

        return $this->query;
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

                    continue;
                }

                $this->query = $this->query->orWhereHas(
                    $table,
                    fn (Builder $query) => $query->where($column, SqlSupport::like($query), '%' . $this->search . '%')
                );
            }
        }
    }
}
