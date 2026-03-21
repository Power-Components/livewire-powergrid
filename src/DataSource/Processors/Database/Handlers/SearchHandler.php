<?php

namespace PowerComponents\LivewirePowerGrid\DataSource\Processors\Database\Handlers;

use Illuminate\Database\Eloquent\{Builder as EloquentBuilder, RelationNotFoundException};
use Illuminate\Database\Query\{Builder as QueryBuilder, JoinClause};
use Illuminate\Support\Facades\Schema;
use PowerComponents\LivewirePowerGrid\{Column, PowerGridComponent};
use PowerComponents\LivewirePowerGrid\DataSource\Support\Sql;
use PowerComponents\LivewirePowerGrid\Support\PowerGridTableCache;
use stdClass;
use Throwable;

class SearchHandler
{
    private string $searchTerm;

    private string $databaseDriver;

    private PowerGridComponent $component;

    public function __construct(PowerGridComponent $component)
    {
        $this->component = $component;
    }

    public function apply(EloquentBuilder|QueryBuilder $query): EloquentBuilder|QueryBuilder
    {
        if ($this->component->search == '') {
            return $query;
        }

        $this->searchTerm = trim(strtolower(htmlspecialchars($this->component->search, ENT_QUOTES | ENT_HTML5, 'UTF-8')));

        $this->databaseDriver = $this->detectDatabaseDriver($query);

        $hasRelationSearch = count($this->component->relationSearch()) && $query instanceof EloquentBuilder;

        $query->where(function (EloquentBuilder|QueryBuilder $subQuery) use ($hasRelationSearch) {
            $modelTable = $subQuery instanceof QueryBuilder ? $subQuery->from : $subQuery->getModel()->getTable();
            $columnList = $this->getColumnList($subQuery, $modelTable);

            collect($this->component->columns)
                ->filter(fn ($column) => (bool) data_get($column, 'searchable'))
                ->each(function ($column) use ($subQuery, $columnList, $hasRelationSearch, $modelTable) {
                    $field = $this->getDataField($column);
                    [$table, $field] = $this->splitField($subQuery, $field);
                    $searchTerm = $this->getBeforeSearchMethod($field, $this->searchTerm);

                    if (empty($table)) {
                        $this->applyWhereByDriver($subQuery, $modelTable, $field, $searchTerm);

                        return;
                    }

                    if (isset($columnList[$field]) || ! $hasRelationSearch) {
                        $this->applyWhereByDriver($subQuery, $table, $field, $searchTerm);
                    }
                });
        });

        if ($hasRelationSearch) {
            $this->filterRelation($query, $this->searchTerm);
        }

        return $query;
    }

    private function filterRelation(EloquentBuilder $query, string $search): void
    {
        foreach ($this->component->relationSearch() as $relation => $columns) {
            if (is_array($columns)) {
                $this->filterNestedRelation($query, $relation, $columns, $search);

                continue;
            }

            $query->orWhereHas($relation, function (EloquentBuilder $subQuery) use ($columns, $search) {
                $searchTerm = $this->getBeforeSearchMethod($columns, $search);
                $tableName = $subQuery->getModel()->getTable();
                $subQuery->where($columns, Sql::like($subQuery), "%{$searchTerm}%");
            });
        }
    }

    private function filterNestedRelation(EloquentBuilder $query, string $relation, array $columns, string $search): void
    {
        foreach ($columns as $nestedRelation => $nestedColumns) {
            if (is_array($nestedColumns)) {
                try {
                    if ($query->getRelation($nestedRelation) != '') {
                        $nestedRelationWithDot = $relation.'.'.$nestedRelation;
                        $query->orWhereHas($nestedRelationWithDot, function (EloquentBuilder $subQuery) use ($nestedColumns, $search) {
                            foreach ($nestedColumns as $nestedColumn) {
                                $searchTerm = $this->getBeforeSearchMethod($nestedColumn, $search);
                                $tableName = $subQuery->getModel()->getTable();
                                $subQuery->where($nestedColumn, Sql::like($subQuery), "%{$searchTerm}%");
                            }
                        });
                    }
                } catch (RelationNotFoundException) {
                    /** @var JoinClause[] $joins */
                    $joins = $query->getQuery()->joins ?? [];
                    $tableExists = collect($joins)->pluck('table')->contains($nestedRelation);

                    if (! $tableExists) {
                        $query->leftJoin($nestedRelation, "$relation.".$nestedRelation.'_id', '=', "$nestedRelation.id");
                    }

                    $query->orWhere(function (EloquentBuilder $subQuery) use ($nestedRelation, $nestedColumns, $search) {
                        foreach ($nestedColumns as $nestedColumn) {
                            $searchTerm = $this->getBeforeSearchMethod($nestedColumn, $search);
                            $subQuery->where("$nestedRelation.$nestedColumn", Sql::like($subQuery), "%{$searchTerm}%");
                        }
                    });
                }

                continue;
            }

            $query->orWhereHas($relation, function (EloquentBuilder $subQuery) use ($nestedColumns, $search) {
                $searchTerm = $this->getBeforeSearchMethod($nestedColumns, $search);
                $tableName = $subQuery->getModel()->getTable();
                $subQuery->where($nestedColumns, Sql::like($subQuery), "%{$searchTerm}%");
            });
        }
    }

    private function getColumnList(EloquentBuilder|QueryBuilder $query, string $modelTable): array
    {
        $connection = $query instanceof EloquentBuilder
            ? $query->getModel()->getConnection()->getName()
            : $query->getConnection()->getName();

        try {
            return PowerGridTableCache::getOrCreate(
                $modelTable,
                fn () => collect(Schema::connection($connection)->getColumns($modelTable))
                    ->pluck('type', 'name')
                    ->toArray()
            );
        } catch (Throwable) {
            return Schema::connection($connection)->getColumnListing($modelTable);
        }
    }

    private function getDataField(Column|stdClass|array $column): string
    {
        return strval(data_get($column, 'dataField')) ?: strval(data_get($column, 'field'));
    }

    private function getBeforeSearchMethod(string $field, ?string $search): ?string
    {
        $method = 'beforeSearch'.str($field)->headline()->replace(' ', '');

        if (method_exists($this->component, $method)) {
            return $this->component->$method($search);
        }

        if (method_exists($this->component, 'beforeSearch')) {
            return $this->component->beforeSearch($field, $search);
        }

        return $search;
    }

    private function splitField(EloquentBuilder|QueryBuilder $query, string $field): array
    {
        $table = $query instanceof QueryBuilder ? $query->from : $query->getModel()->getTable();

        if (str_contains($field, '.')) {
            $explodeField = explode('.', $field);
            $table = $explodeField[0];
            $field = $explodeField[1];
        }

        return [$table, $field];
    }

    private function applyWhereByDriver(EloquentBuilder|QueryBuilder $query, string $table, string $field, string $searchTerm): void
    {
        $fullField = "{$table}.{$field}";

        switch ($this->databaseDriver) {
            case 'oracle':
                // Oracle necesita UPPER() explícito
                $query->orWhereRaw("UPPER({$fullField}) LIKE UPPER(?)", ["%{$searchTerm}%"]);
                $this->suggestFunctionalIndex($table, $field);
                break;

            case 'sqlsrv':
                $query->orWhereRaw("{$fullField} COLLATE Latin1_General_CI_AI LIKE ?", ["%{$searchTerm}%"]);
                break;

            default:
                $query->orWhere($fullField, Sql::like($query), "%{$searchTerm}%");
                break;
        }
    }

    private function detectDatabaseDriver(EloquentBuilder|QueryBuilder $query): string
    {
        if ($query instanceof EloquentBuilder) {
            $connection = $query->getModel()->getConnection();
        } else {
            $connection = $query->getConnection();
        }

        return $connection->getDriverName();
    }

    private function suggestFunctionalIndex(string $table, string $field): void
    {
        if (app()->environment('local', 'development')) {
            logger()->notice("PowerGrid/Oracle: Consider creating this functional index:
                CREATE INDEX idx_{$table}_{$field}_upper ON {$table}(UPPER({$field}))");
        }
    }

    private function suggestPgTrgm(string $table, string $field): void
    {
        if (app()->environment('local', 'development')) {
            logger()->notice("PowerGrid/PostgreSQL: for leading wildcards, install pg_trgm and create the index:
                CREATE EXTENSION IF NOT EXISTS pg_trgm;
                CREATE INDEX idx_{$table}_{$field}_trgm ON {$table} USING gin (UPPER({$field}) gin_trgm_ops);");
        }
    }
}
