<?php

namespace PowerComponents\LivewirePowerGrid\DataSource;

use Illuminate\Database\Eloquent\{Builder as EloquentBuilder, RelationNotFoundException};
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;

use PowerComponents\LivewirePowerGrid\Components\Filters\{Builders\Number};
use PowerComponents\LivewirePowerGrid\{Column,
    Components\Filters\Builders\Boolean,
    Components\Filters\Builders\DatePicker,
    Components\Filters\Builders\DateTimePicker,
    Components\Filters\Builders\InputText,
    Components\Filters\Builders\MultiSelect,
    Components\Filters\Builders\Select,
    DataSource\Support\InputOperators,
    DataSource\Support\Sql,
    PowerGridComponent,
    Support\PowerGridTableCache};
use stdClass;
use Throwable;

class Builder
{
    use InputOperators;

    public function __construct(
        private EloquentBuilder|QueryBuilder $query,
        private readonly PowerGridComponent $component
    ) {
    }

    public static function make(
        EloquentBuilder|QueryBuilder $query,
        PowerGridComponent $component
    ): Builder {
        return new Builder($query, $component);
    }

    public function filter(): EloquentBuilder|QueryBuilder
    {
        // To make it work on export, we need to use ->filters instead of filters()
        $filters = collect(
            app()->runningInConsole() && !app()->runningUnitTests()
            ? $this->component->filters
            : $this->component->filters()
        );

        if ($filters->isEmpty()) {
            return $this->query;
        }

        foreach ($this->component->filters as $filterType => $columns) {
            $columns = Arr::dot($columns);

            // convert array:2 [
            //    "dishes.produced_at.start" => "2021-03-03",
            //    "dishes.produced_at.end" => "2021-03-01"
            //] to
            // array:2 [
            //    "dishes.produced_at" => ["start" => "2021-03-03"],
            //    "dishes.produced_at.end" => ["start" => "2021-03-01"]
            //] and
            // convert array:2 [
            //    "dishes.produced_at.0" => "2021-03-03",
            //    "dishes.produced_at.1" => "2021-03-01"
            //] to
            // array:2 [
            //    "dishes.produced_at" => [0 => "2021-03-03"],
            //    "dishes.produced_at" => [1 => "2021-03-01"]
            //]

            $newColumns = [];

            foreach ($columns as $key => $value) {
                $parts    = explode('.', $key);
                $lastPart = end($parts);

                if (is_numeric($lastPart) && intval($lastPart) == $lastPart) {
                    array_pop($parts);
                    $prefix = implode('.', $parts);

                    if (!isset($newColumns[$prefix])) {
                        $newColumns[$prefix] = [];
                    }

                    $index = intval($lastPart);

                    $newColumns[$prefix][$index] = $value;
                } elseif ($lastPart === 'start' || $lastPart === 'end') {
                    $prefix = implode('.', array_slice($parts, 0, -1));

                    if (!isset($newColumns[$prefix])) {
                        $newColumns[$prefix] = [];
                    }

                    $newColumns[$prefix][$lastPart] = $value;
                } else {
                    $newColumns[$key] = $value;
                }
            }

            foreach ($newColumns as $field => $value) {
                $this->query->where(function ($query) use ($filterType, $field, $value, $filters) {
                    $filter = function ($query, $filters, $filterType, $field, $value) {
                        $filter = $filters->filter(function ($filter) use ($field) {
                            return data_get($filter, 'field') === $field;
                        })
                            ->first();

                        match ($filterType) {
                            'datetime'     => (new DateTimePicker($this->component, $filter))->builder($query, $field, $value),
                            'date'         => (new DatePicker($this->component, $filter))->builder($query, $field, $value),
                            'multi_select' => (new MultiSelect($this->component, $filter))->builder($query, $field, $value),
                            'select'       => (new Select($this->component, $filter))->builder($query, $field, $value),
                            'boolean'      => (new Boolean($this->component, $filter))->builder($query, $field, $value),
                            'number'       => (new Number($this->component, $filter))->builder($query, $field, $value),
                            'input_text'   => (new InputText($this->component, $filter))->builder($query, $field, [
                                'selected'     => $this->validateInputTextOptions($this->component->filters, $field),
                                'value'        => $value,
                                'searchMorphs' => $this->component->searchMorphs(),
                            ]),
                            default => null
                        };
                    };

                    $filter($query, $filters, $filterType, $field, $value);
                });
            }
        }

        return $this->query;
    }

    public function filterContains(): Builder
    {
        if ($this->component->search == '') {
            return $this;
        }

        $search            = trim(strtolower(htmlspecialchars($this->component->search, ENT_QUOTES | ENT_HTML5, 'UTF-8')));
        $hasRelationSearch = count($this->component->relationSearch()) && $this->query instanceof EloquentBuilder;

        $this->query = $this->query->where(
            function (EloquentBuilder|QueryBuilder $query) use ($search, $hasRelationSearch) {
                /** @var string $modelTable */
                $modelTable = $query instanceof QueryBuilder ? $query->from : $query->getModel()->getTable();
                $columnList = $this->getColumnList($modelTable);

                collect($this->component->columns)
                    ->filter(fn (stdClass|array|Column $column) => (bool) data_get($column, 'searchable'))
                    ->each(function (stdClass|array|Column $column) use ($query, $search, $columnList, $hasRelationSearch) {
                        $field = $this->getDataField($column);

                        [$table, $field] = $this->splitField($field);

                        $search = $this->getBeforeSearchMethod($field, $search);

                        $hasColumn = isset($columnList[$field]);

                        if (empty($table)) {
                            return $query->orWhere($field, Sql::like($query), "%{$search}%");
                        }

                        if ($hasColumn || !$hasRelationSearch) {
                            return $query->orWhere("{$table}.{$field}", Sql::like($query), "%{$search}%");
                        }
                    });

                return $query;
            }
        );

        if ($hasRelationSearch) {
            $this->filterRelation($search);
        }

        return $this;
    }

    private function filterRelation(string $search): void
    {
        /** @var EloquentBuilder $query */
        $query = $this->query;

        foreach ($this->component->relationSearch() as $table => $columns) {
            if (is_array($columns)) {
                $this->filterNestedRelation($table, $columns, $search);

                continue;
            }

            $query->orWhereHas($table, function (EloquentBuilder $query) use ($columns, $search) {
                $search = $this->getBeforeSearchMethod($columns, $search);
                $query->when(
                    $search,
                    fn (EloquentBuilder $query) => $query->where($columns, Sql::like($query), '%' . $search . '%')
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
                            /** @var string $nestedColumn */
                            foreach ($nestedColumns as $nestedColumn) {
                                $search = $this->getBeforeSearchMethod($nestedColumn, $search);
                                $query->when(
                                    $search,
                                    fn (EloquentBuilder $query) => $query->where("$nestedTableWithDot.$nestedColumn", Sql::like($query), '%' . $search . '%')
                                );
                            }
                        });
                    }
                } catch (RelationNotFoundException $e) {
                    $query->leftJoin($nestedTable, "$table.$nestedTable" . '_id', '=', "$nestedTable.id")
                        ->orWhere(function (EloquentBuilder $query) use ($nestedTable, $nestedColumns, $search) {
                            foreach ($nestedColumns as $nestedColumn) {
                                $search = $this->getBeforeSearchMethod($nestedColumn, $search);
                                $query->when(
                                    $search,
                                    fn (EloquentBuilder $query) => $query->where("$nestedTable.$nestedColumn", Sql::like($query), '%' . $search . '%')
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
                    fn (EloquentBuilder $query) => $query->where($nestedColumns, Sql::like($query), '%' . $search . '%')
                );
            });
        }

        $this->query = $query;
    }

    private function getColumnList(string $modelTable): array
    {
        $connection = $this->query instanceof EloquentBuilder
            ? $this->query->getModel()->getConnection()->getName()
            /** @phpstan-ignore-next-line  */
            : $this->query->getConnection()->getConfig('name');

        try {
            return PowerGridTableCache::getOrCreate(
                $modelTable,
                fn () => collect(Schema::connection($connection)->getColumns($modelTable))
                    ->pluck('type', 'name')
                    ->toArray()
            );
        } catch (Throwable $throwable) {
            logger()->warning('PowerGrid [getColumnList] warning: ', [
                'table'     => $modelTable,
                'throwable' => $throwable->getTrace(),
            ]);

            return Schema::connection($connection)->getColumnListing($modelTable);
        }
    }

    private function getDataField(Column|stdClass|array $column): string
    {
        return strval(data_get($column, 'dataField')) ?: strval(data_get($column, 'field'));
    }

    private function getBeforeSearchMethod(string $field, ?string $search): ?string
    {
        $method = 'beforeSearch' . str($field)->headline()->replace(' ', '');

        if (method_exists($this->component, $method)) {
            return $this->component->$method($search);
        }

        if (method_exists($this->component, 'beforeSearch')) {
            return $this->component->beforeSearch($field, $search);
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
}
