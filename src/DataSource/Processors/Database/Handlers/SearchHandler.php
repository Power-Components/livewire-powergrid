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
    public function __construct(
        private readonly PowerGridComponent $component
    ) {}

    public function apply(EloquentBuilder|QueryBuilder $query): EloquentBuilder|QueryBuilder
    {
        if ($this->component->search == '') {
            return $query;
        }

        $search = trim(strtolower(htmlspecialchars($this->component->search, ENT_QUOTES | ENT_HTML5, 'UTF-8')));
        $hasRelationSearch = count($this->component->relationSearch()) && $query instanceof EloquentBuilder;

        $query->where(function (EloquentBuilder|QueryBuilder $subQuery) use ($search, $hasRelationSearch) {
            $modelTable = $subQuery instanceof QueryBuilder ? $subQuery->from : $subQuery->getModel()->getTable();
            $columnList = $this->getColumnList($subQuery, $modelTable);

            collect($this->component->columns)
                ->filter(fn (stdClass|array|Column $column) => (bool) data_get($column, 'searchable'))
                ->each(function (stdClass|array|Column $column) use ($subQuery, $search, $columnList, $hasRelationSearch) {
                    $field = $this->getDataField($column);
                    [$table, $field] = $this->splitField($subQuery, $field);
                    $search = $this->getBeforeSearchMethod($field, $search);

                    if (empty($table)) {
                        $subQuery->orWhere($field, Sql::like($subQuery), "%{$search}%");

                        return;
                    }

                    if (isset($columnList[$field]) || ! $hasRelationSearch) {
                        $subQuery->orWhere("{$table}.{$field}", Sql::like($subQuery), "%{$search}%");
                    }
                });
        });

        if ($hasRelationSearch) {
            $this->filterRelation($query, $search);
        }

        return $query;
    }

    private function filterRelation(EloquentBuilder $query, string $search): void
    {
        foreach ($this->component->relationSearch() as $table => $columns) {
            if (is_array($columns)) {
                $this->filterNestedRelation($query, $table, $columns, $search);

                continue;
            }

            $query->orWhereHas($table, function (EloquentBuilder $subQuery) use ($columns, $search) {
                $search = $this->getBeforeSearchMethod($columns, $search);
                $subQuery->when($search, fn ($q) => $q->where($columns, Sql::like($q), '%'.$search.'%'));
            });
        }
    }

    private function filterNestedRelation(EloquentBuilder $query, string $table, array $columns, string $search): void
    {
        foreach ($columns as $nestedTable => $nestedColumns) {
            if (is_array($nestedColumns)) {
                try {
                    if ($query->getRelation($nestedTable) != '') {
                        $nestedTableWithDot = $table.'.'.$nestedTable;
                        $query->orWhereHas($nestedTableWithDot, function (EloquentBuilder $subQuery) use ($nestedColumns, $search) {
                            foreach ($nestedColumns as $nestedColumn) {
                                $search = $this->getBeforeSearchMethod($nestedColumn, $search);
                                $subQuery->when($search, fn ($q) => $q->where($nestedColumn, Sql::like($q), '%'.$search.'%'));
                            }
                        });
                    }
                } catch (RelationNotFoundException) {
                    /** @var JoinClause[] $joins */
                    $joins = $query->getQuery()->joins ?? [];
                    $tableExists = collect($joins)->pluck('table')->contains($nestedTable);

                    if (! $tableExists) {
                        $query->leftJoin($nestedTable, "$table.".$nestedTable.'_id', '=', "$nestedTable.id");
                    }

                    $query->orWhere(function (EloquentBuilder $subQuery) use ($nestedTable, $nestedColumns, $search) {
                        foreach ($nestedColumns as $nestedColumn) {
                            $search = $this->getBeforeSearchMethod($nestedColumn, $search);
                            $subQuery->when($search, fn ($q) => $q->where("$nestedTable.$nestedColumn", Sql::like($q), '%'.$search.'%'));
                        }
                    });
                }

                continue;
            }

            $query->orWhereHas($table, function (EloquentBuilder $subQuery) use ($nestedColumns, $search) {
                $search = $this->getBeforeSearchMethod($nestedColumns, $search);
                $subQuery->when($search, fn ($q) => $q->where($nestedColumns, Sql::like($q), '%'.$search.'%'));
            });
        }
    }

    private function getColumnList(EloquentBuilder|QueryBuilder $query, string $modelTable): array
    {
        $connection = $query instanceof EloquentBuilder
            ? $query->getModel()->getConnection()->getName()
            : $query->getConnection()->getConfig('name');

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
}
