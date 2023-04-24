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
            $search = strtolower($search);

            $this->query = $this->query->where(function (Builder $query) use ($search) {
                $modelTable = $query->getModel()->getTable();
                $columnList = $this->getColumnList($modelTable);

                collect($this->powerGridComponent->columns)
                    ->filter(fn ($column) => $this->isSearchableColumn($column))
                    ->each(function ($column) use ($query, $search, $columnList) {
                        $field = $this->getDataField($column);

                        [$table, $field] = $this->splitField($field);

                        $search = $this->getBeforeSearchMethod($field, $search);

                        $hasColumn = in_array($field, $columnList, true);

                        $query->when($search, function () use ($column, $query, $search, $table, $field, $hasColumn) {
                            if (($sqlRaw = strval(data_get($column, 'searchableRaw')))) {
                                $query->orWhereRaw($sqlRaw . ' ' . SqlSupport::like($query) . ' \'%' . $search . '%\'');
                            }

                            if ($hasColumn && blank(data_get($column, 'searchableRaw')) && $search) {
                                try {
                                    $columnType = DB::getSchemaBuilder()->getColumnType($table, $field);

                                    $driverName = $query->getModel()->getConnection()->getDriverName();

                                    if ($columnType === 'json' && strtolower($driverName) !== 'pgsql') {
                                        $query->orWhereRaw("LOWER(`{$table}`.`{$field}`)" . SqlSupport::like($query) . "?", '%' . $search . '%');
                                    } else {
                                        $query->orWhere("{$table}.{$field}", SqlSupport::like($query), "%{$search}%");
                                    }
                                } catch (\Throwable) {
                                    $query->orWhere("{$table}.{$field}", SqlSupport::like($query), "%{$search}%");
                                }
                            }
                        });
                    });

                return $query;
            });

            if (count($this->powerGridComponent->relationSearch) && $search) {
                $this->filterRelation($search);
            }
        }

        return $this;
    }

    private function filterRelation(string $search): void
    {
        foreach ($this->powerGridComponent->relationSearch as $table => $columns) {
            if (is_array($columns)) {
                $this->filterNestedRelation($table, $columns, $search);

                continue;
            }

            $this->query->orWhereHas($table, function ($query) use ($columns, $search) {
                $search = $this->getBeforeSearchMethod($columns, $search);
                $query->where($columns, SqlSupport::like($query), '%' . $search . '%');
            });
        }
    }

    private function filterNestedRelation(string $table, array $columns, string $search): void
    {
        foreach ($columns as $nestedTable => $nestedColumns) {
            if (is_array($nestedColumns)) {
                if ($this->query->getRelation($nestedTable) != '') {
                    $nestedTableWithDot = $table . '.' . $nestedTable;
                    $this->query->orWhereHas($nestedTableWithDot, function ($query) use ($nestedTableWithDot, $nestedColumns, $search) {
                        foreach ($nestedColumns as $nestedColumn) {
                            $search = $this->getBeforeSearchMethod($nestedColumn, $search);
                            $query->where("$nestedTableWithDot.$nestedColumn", SqlSupport::like($query), '%' . $search . '%');
                        }
                    });
                }

                continue;
            }

            $this->query->orWhereHas($table, function ($query) use ($nestedColumns, $search) {
                $search = $this->getBeforeSearchMethod($nestedColumns, $search);
                $query->where($nestedColumns, SqlSupport::like($query), '%' . $search . '%');
            });
        }
    }

    private function isSearchableColumn(Column|\stdClass $column): bool
    {
        return boolval(data_get($column, 'searchable')) || strval(data_get($column, 'searchableRaw')) !== '';
    }

    private function getDataField(Column|\stdClass $column): string
    {
        return strval(data_get($column, 'dataField')) ?: strval(data_get($column, 'field'));
    }

    private function getBeforeSearchMethod(string $field, ?string $search): ?string
    {
        $method = 'beforeSearch' . str($field)->headline()->replace(' ', '');

        if (method_exists($this->powerGridComponent, $method)) {
            return $this->powerGridComponent->$method($search);
        }

        if (method_exists($this->powerGridComponent, 'beforeSearch')) {
            return $this->powerGridComponent->beforeSearch($field, $search);
        }

        return $search;
    }

    private function splitField(string $field): array
    {
        $table = $this->query->getModel()->getTable();

        if (str_contains($field, '.')) {
            $explodeField = explode('.', $field);
            $table        = $explodeField[0];
            $field        = $explodeField[1];
        }

        return [$table, $field];
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
