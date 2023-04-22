<?php

namespace PowerComponents\LivewirePowerGrid\Helpers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\{Cache, DB, Schema};
use Illuminate\Support\{Arr, Str};
use PowerComponents\LivewirePowerGrid\Filters\{Builders\Boolean,
    Builders\DatePicker,
    Builders\DateTimePicker,
    Builders\InputText,
    Builders\MultiSelect,
    Builders\Number,
    Builders\Select};
use PowerComponents\LivewirePowerGrid\{Column, PowerGridComponent};

class Model
{
    use InputOperators;

    public function __construct(
        private Builder $query,
        private readonly PowerGridComponent $powerGridComponent
    ) {
    }

    public static function make(Builder $query, PowerGridComponent $powerGridComponent): Model
    {
        return new Model($query, $powerGridComponent);
    }

    public function filter(): Builder
    {
        $filters = collect($this->powerGridComponent->filters())->flatten()->filter()->values();

        if ($filters->isEmpty()) {
            return $this->query;
        }

        foreach ($this->powerGridComponent->filters as $filterType => $column) {
            $this->query->where(function ($query) use ($filterType, $column, $filters) {
                $filter = function ($query, $filters, $filterType, $field, $value) {
                    $filter = $filters->filter(fn ($filter) => $filter->field === $field)
                        ->first();

                    match ($filterType) {
                        'datetime'     => (new DateTimePicker($filter))->builder($query, $field, $value),
                        'date'         => (new DatePicker($filter))->builder($query, $field, $value),
                        'multi_select' => (new MultiSelect($filter))->builder($query, $field, $value),
                        'select'       => (new Select($filter))->builder($query, $field, $value),
                        'boolean'      => (new Boolean($filter))->builder($query, $field, $value),
                        'number'       => (new Number($filter))->builder($query, $field, $value),
                        'input_text'   => (new InputText($filter))->builder($query, $field, [
                            'selected'     => $this->validateInputTextOptions($this->powerGridComponent->filters, $field),
                            'value'        => $value,
                            'searchMorphs' => $this->powerGridComponent->searchMorphs,
                        ]),
                        default => null
                    };
                };

                if (
                    isset($column[key($column)]) &&
                    is_array($column[key($column)]) &&
                    is_string(key($column[key($column)])) &&
                    count($column[key($column)]) === 1
                ) {
                    $field = key(Arr::dot($column));

                    $value = Arr::dot($column)[$field];

                    $filter($query, $filters, $filterType, $field, $value);
                } else {
                    foreach ($column as $field => $value) {
                        $filter($query, $filters, $filterType, $field, $value);
                    }
                }
            });
        }

        return $this->query;
    }

    public function filterContains(): Model
    {
        if ($this->powerGridComponent->search != '') {
            $search = $this->powerGridComponent->search;
            $search = htmlspecialchars($search, ENT_QUOTES | ENT_HTML5, 'UTF-8');

            if (method_exists($this->powerGridComponent, 'beforeSearchContains')) {
                $search = $this->powerGridComponent->beforeSearchContains($search);
            }

            $this->query = $this->query->where(function (Builder $query) use ($search) {
                $modelTable = $query->getModel()->getTable();

                $columnList = $this->getColumnList($modelTable);

                /** @var Column $column */
                foreach ($this->powerGridComponent->columns as $column) {
                    $searchable = strval(data_get($column, 'searchable'));
                    $table      = $modelTable;
                    $field      = strval(data_get($column, 'dataField')) ?: strval(data_get($column, 'field'));

                    if ($searchable && $field) {
                        if (str_contains($field, '.')) {
                            $explodeField = Str::of($field)->explode('.');
                            $table        = strval($explodeField->get(0));
                            $field        = strval($explodeField->get(1));
                        }

                        $hasColumn = in_array($field, $columnList, true);

                        if ($hasColumn) {
                            try {
                                $columnType = DB::getSchemaBuilder()->getColumnType($table, $field);
                                $driverName = $query->getModel()->getConnection()->getDriverName();

                                if ($columnType === 'json' && strtolower($driverName) !== 'pgsql') {
                                    $query->orWhereRaw("LOWER(`{$table}`.`{$field}`)" . SqlSupport::like($query) . ":search", ['search' => "%{$search}%"]);
                                } else {
                                    $query->orWhere("{$table}.{$field}", SqlSupport::like($query), "%{$search}%");
                                }
                            } catch (\Throwable) {
                                $query->orWhere("{$table}.{$field}", SqlSupport::like($query), "%{$search}%");
                            }
                        }

                        if ($sqlRaw = strval(data_get($column, 'searchableRaw'))) {
                            $query->orWhereRaw($sqlRaw . ' ' . SqlSupport::like($query) . ' \'%' . $search . '%\'');
                        }
                    }
                }

                return $query;
            });

            if (count($this->powerGridComponent->relationSearch)) {
                $this->filterRelation();
            }
        }

        return $this;
    }

    private function filterRelation(): void
    {
        foreach ($this->powerGridComponent->relationSearch as $table => $columns) {
            if (is_array($columns)) {
                $this->filterNestedRelation($table, $columns);

                continue;
            }

            $this->query->orWhereHas($table, function ($query) use ($columns) {
                $query->where($columns, SqlSupport::like($query), '%' . $this->powerGridComponent->search . '%');
            });
        }
    }

    private function filterNestedRelation(string $table, array $columns): void
    {
        foreach ($columns as $nestedTable => $nestedColumns) {
            if (is_array($nestedColumns)) {
                if ($this->query->getRelation($nestedTable) != '') {
                    $nestedTableWithDot = $table . '.' . $nestedTable;
                    $this->query->orWhereHas($nestedTableWithDot, function ($query) use ($nestedTableWithDot, $nestedColumns) {
                        foreach ($nestedColumns as $nestedColumn) {
                            $query->where("$nestedTableWithDot.$nestedColumn", SqlSupport::like($query), '%' . $this->powerGridComponent->search . '%');
                        }
                    });
                }
            } else {
                $this->query->orWhereHas($table, function ($query) use ($nestedColumns) {
                    $query->where($nestedColumns, SqlSupport::like($query), '%' . $this->powerGridComponent->search . '%');
                });
            }
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
}
