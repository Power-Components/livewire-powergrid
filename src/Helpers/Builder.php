<?php

namespace PowerComponents\LivewirePowerGrid\Helpers;

use Illuminate\Database\Eloquent\{Builder as EloquentBuilder,
    RelationNotFoundException};
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\{Cache, DB, Schema};
use PowerComponents\LivewirePowerGrid\Filters\{Builders\Boolean,
    Builders\DatePicker,
    Builders\DateTimePicker,
    Builders\InputText,
    Builders\MultiSelect,
    Builders\Number,
    Builders\Select};
use PowerComponents\LivewirePowerGrid\{Column, PowerGridComponent};

class Builder
{
    use InputOperators;

    public function __construct(
        private EloquentBuilder|QueryBuilder $query,
        private readonly PowerGridComponent $powerGridComponent
    ) {
    }

    public static function make(EloquentBuilder|QueryBuilder $query, PowerGridComponent $powerGridComponent): Builder
    {
        return new Builder($query, $powerGridComponent);
    }

    public function filter(): EloquentBuilder|QueryBuilder
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
                    if (count($column) > 1) {
                        foreach ($column as $tableName => $columnValue) {
                            $field = key(Arr::dot($columnValue));

                            $value = Arr::dot($columnValue)[$field];

                            $filter($query, $filters, $filterType, $tableName . '.' . $field, $value);
                        }
                    } else {
                        $field = key(static::arrayToDot($column));

                        $value = static::arrayToDot($column)[$field];

                        $filter($query, $filters, $filterType, $field, $value);
                    }
                } else {
                    foreach ($column as $field => $value) {
                        if (is_array($value) && $filterType === 'input_text') {
                            foreach ($value as $arrayField => $arrayValue) {
                                $filter($query, $filters, $filterType, $arrayField, $arrayValue);
                            }
                        } else {
                            $filter($query, $filters, $filterType, $field, $value);
                        }
                    }
                }
            });
        }

        return $this->query;
    }

    public static function arrayToDot(array $array, string $prepend = ''): array
    {
        $results = [];

        foreach ($array as $key => $value) {
            if (is_array($value) && !empty($value)) {
                if (is_numeric(array_key_first($value))) {
                    $results[$prepend . $key] = $value;

                    break;
                }

                $results = array_merge($results, static::arrayToDot($value, $prepend . $key . '.'));

                continue;
            }

            $results[$prepend . $key] = $value;
        }

        return $results;
    }

    public function filterContains(): Builder
    {
        if ($this->powerGridComponent->search != '') {
            $search = $this->powerGridComponent->search;
            $search = htmlspecialchars($search, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $search = strtolower($search);

            $this->query = $this->query->where(function (EloquentBuilder|QueryBuilder $query) use ($search) {
                if ($query instanceof QueryBuilder) {
                    $modelTable = $query->from;
                } else {
                    $modelTable = $query->getModel()->getTable();
                }

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

                                    /** @phpstan-ignore-next-line  */
                                    $driverName = $query->getConnection()->getConfig('driver');

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

            if (count($this->powerGridComponent->relationSearch) && $this->query instanceof EloquentBuilder) {
                $this->filterRelation($search);
            }
        }

        return $this;
    }

    private function filterRelation(string $search): void
    {
        /** @var EloquentBuilder $query */
        $query = $this->query;

        foreach ($this->powerGridComponent->relationSearch as $table => $columns) {
            if (is_array($columns)) {
                $this->filterNestedRelation($table, $columns, $search);

                continue;
            }

            $query->orWhereHas($table, function (EloquentBuilder $query) use ($columns, $search) {
                $search = $this->getBeforeSearchMethod($columns, $search);
                $query->when(
                    $search,
                    fn (EloquentBuilder $query) => $query->where($columns, SqlSupport::like($query), '%' . $search . '%')
                );
            });
        }

        $this->query = $query;
    }

    private function filterNestedRelation(string $table, array $columns, string $search): void
    {
        /** @var EloquentBuilder $query */
        $query = $this->query;

        foreach ($columns as $nestedTable => $nestedColumns) {
            if (is_array($nestedColumns)) {
                try {
                    if ($query->getRelation($nestedTable) != '') {
                        $nestedTableWithDot = $table . '.' . $nestedTable;
                        $query->orWhereHas($nestedTableWithDot, function (EloquentBuilder $query) use ($nestedTableWithDot, $nestedColumns, $search) {
                            foreach ($nestedColumns as $nestedColumn) {
                                $search = $this->getBeforeSearchMethod($nestedColumn, $search);
                                $query->when(
                                    $search,
                                    fn (EloquentBuilder $query) => $query->where("$nestedTableWithDot.$nestedColumn", SqlSupport::like($query), '%' . $search . '%')
                                );
                            }
                        });
                    }
                } catch (RelationNotFoundException $e) {
                    $query->leftJoin($nestedTable, "$table.$nestedTable" . "_id", '=', "$nestedTable.id")
                        ->orWhere(function (EloquentBuilder $query) use ($nestedTable, $nestedColumns, $search) {
                            foreach ($nestedColumns as $nestedColumn) {
                                $search = $this->getBeforeSearchMethod($nestedColumn, $search);
                                $query->when(
                                    $search,
                                    fn (EloquentBuilder $query) => $query->where("$nestedTable.$nestedColumn", SqlSupport::like($query), '%' . $search . '%')
                                );
                            }
                        });
                }

                continue;
            }

            $query->orWhereHas($table, function (EloquentBuilder $query) use ($nestedColumns, $search) {
                $search = $this->getBeforeSearchMethod($nestedColumns, $search);

                $query->when(
                    $search,
                    fn (EloquentBuilder $query) => $query->where($nestedColumns, SqlSupport::like($query), '%' . $search . '%')
                );
            });
        }

        $this->query = $query;
    }

    private function isSearchableColumn(Column|\stdClass|array $column): bool
    {
        return boolval(data_get($column, 'searchable')) || strval(data_get($column, 'searchableRaw')) !== '';
    }

    private function getDataField(Column|\stdClass|array $column): string
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
        if ($this->query instanceof QueryBuilder) {
            $table = $this->query->from;
        } else {
            $table = $this->query->getModel()->getTable();
        }

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
