<?php

namespace PowerComponents\LivewirePowerGrid\DataSource\Processors\Database\Handlers;

use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\{Builder as EloquentBuilder, RelationNotFoundException};
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\{Builder as QueryBuilder, JoinClause};
use Illuminate\Support\Facades\Schema;
use PowerComponents\LivewirePowerGrid\{Column, PowerGridComponent};
use PowerComponents\LivewirePowerGrid\DataSource\Support\Sql;
use PowerComponents\LivewirePowerGrid\Support\PowerGridTableCache;
use stdClass;
use Throwable;

class SearchHandler implements SearchHandlerContract
{
    public function __construct(
        protected readonly PowerGridComponent $component
    ) {}

    /** @param  EloquentBuilder<Model>|QueryBuilder  $query
     * @return EloquentBuilder<Model>|QueryBuilder */
    public function apply(EloquentBuilder|QueryBuilder $query): EloquentBuilder|QueryBuilder
    {
        if ($this->component->search == '') {
            return $query;
        }

        $search = trim(strtolower(htmlspecialchars($this->component->search, ENT_QUOTES | ENT_HTML5, 'UTF-8')));
        $hasRelationSearch = count($this->component->relationSearch()) && $query instanceof EloquentBuilder;

        $query->where(function (EloquentBuilder|QueryBuilder $subQuery) use ($search, $hasRelationSearch) {
            /** @var string $modelTable */
            $modelTable = $subQuery instanceof QueryBuilder ? $subQuery->from : $subQuery->getModel()->getTable();
            $columnList = $this->getColumnList($subQuery, $modelTable);

            collect($this->component->columns)
                ->filter(function (mixed $column): bool {
                    return (bool) data_get($column, 'searchable');
                })
                ->each(function (mixed $column) use ($subQuery, $search, $columnList, $hasRelationSearch) {
                    /** @var Column|stdClass|array<string, mixed> $column */
                    $field = $this->getDataField($column);
                    [$table, $field] = $this->splitField($subQuery, $field);
                    $search = $this->getBeforeSearchMethod($field, $search);

                    if (empty($table)) {
                        $subQuery->orWhere($field, Sql::like($subQuery), '%'.($search ?? '').'%');

                        return;
                    }

                    if (isset($columnList[$field]) || ! $hasRelationSearch) {
                        $subQuery->orWhere($table.'.'.$field, Sql::like($subQuery), '%'.($search ?? '').'%');
                    }
                });
        });

        if ($hasRelationSearch) {
            $this->filterRelation($query, $search);
        }

        return $query;
    }

    /** @param  EloquentBuilder<Model>  $query */
    protected function filterRelation(EloquentBuilder $query, string $search): void
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

    /** @param  EloquentBuilder<Model>  $query
     * @param  array<string|int, mixed>  $columns */
    /** @param  EloquentBuilder<Model>  $query
     * @param  array<string|int, mixed>  $columns */
    protected function filterNestedRelation(EloquentBuilder $query, string $table, array $columns, string $search): void
    {
        foreach ($columns as $nestedTable => $nestedColumns) {
            if (is_array($nestedColumns)) {
                try {
                    /** @var string $nestedTable */
                    if ($query->getRelation($nestedTable) != '') {
                        $nestedTableWithDot = $table.'.'.$nestedTable;
                        $query->orWhereHas($nestedTableWithDot, function (EloquentBuilder $subQuery) use ($nestedColumns, $search) {
                            foreach ($nestedColumns as $nestedColumn) {
                                /** @var string $nestedColumn */
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
                            /** @var string $nestedColumn */
                            $search = $this->getBeforeSearchMethod($nestedColumn, $search);
                            $subQuery->when($search, fn ($q) => $q->where($nestedTable.'.'.$nestedColumn, Sql::like($q), '%'.($search ?? '').'%'));
                        }
                    });
                }

                continue;
            }

            $query->orWhereHas($table, function (EloquentBuilder $subQuery) use ($nestedColumns, $search) {
                /** @var string $nestedColumns */
                $search = $this->getBeforeSearchMethod($nestedColumns, $search);
                $subQuery->when($search, fn ($q) => $q->where($nestedColumns, Sql::like($q), '%'.$search.'%'));
            });
        }
    }

    /** @param  EloquentBuilder<Model>|QueryBuilder  $query
     * @return array<string|int, mixed> */
    protected function getColumnList(EloquentBuilder|QueryBuilder $query, string $modelTable): array
    {
        $conn = $query instanceof EloquentBuilder
            ? $query->getModel()->getConnection()
            : $query->getConnection();
        $connection = $conn instanceof Connection ? $conn->getName() : null;

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

    /** @param  Column|stdClass|array<string, mixed>  $column */
    protected function getDataField(Column|stdClass|array $column): string
    {
        /** @var string $dataField */
        $dataField = data_get($column, 'dataField');
        /** @var string $field */
        $field = data_get($column, 'field');

        return $dataField ?: $field;
    }

    protected function getBeforeSearchMethod(string $field, ?string $search): ?string
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

    /** @param  EloquentBuilder<Model>|QueryBuilder  $query
     * @return array{string|null, string} */
    protected function splitField(EloquentBuilder|QueryBuilder $query, string $field): array
    {
        $from = $query instanceof QueryBuilder ? $query->from : $query->getModel()->getTable();
        $table = is_string($from) ? $from : null;

        if (str_contains($field, '.')) {
            $explodeField = explode('.', $field);
            $table = $explodeField[0];
            $field = $explodeField[1];
        }

        return [$table, $field];
    }
}
