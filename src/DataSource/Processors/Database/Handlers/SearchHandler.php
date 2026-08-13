<?php

namespace PowerComponents\LivewirePowerGrid\DataSource\Processors\Database\Handlers;

use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\{Builder as EloquentBuilder, RelationNotFoundException};
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\{Builder as QueryBuilder, JoinClause};
use PowerComponents\LivewirePowerGrid\{Column, Contracts\PowerGridContext, Contracts\SchemaInspector};
use PowerComponents\LivewirePowerGrid\DataSource\Support\Sql;
use stdClass;
use Throwable;

class SearchHandler implements SearchHandlerContract
{
    public function __construct(
        protected readonly PowerGridContext $component
    ) {}

    /** @param  EloquentBuilder<Model>|QueryBuilder  $query
     * @return EloquentBuilder<Model>|QueryBuilder */
    public function apply(EloquentBuilder|QueryBuilder $query): EloquentBuilder|QueryBuilder
    {
        $searchTerm = $this->component->state()->search;

        if ($searchTerm == '') {
            return $query;
        }

        $search = trim(strtolower(htmlspecialchars($searchTerm, ENT_QUOTES | ENT_HTML5, 'UTF-8')));
        $hasRelationSearch = count($this->component->relationSearch()) && $query instanceof EloquentBuilder;

        $query->where(function (EloquentBuilder|QueryBuilder $subQuery) use ($search, $hasRelationSearch) {
            /** @var string $modelTable */
            $modelTable = $subQuery instanceof QueryBuilder ? $subQuery->from : $subQuery->getModel()->getTable();
            $columnList = $this->getColumnList($subQuery, $modelTable);

            // The `columns` property is mass-assignable and hydrated from the
            // client snapshot, so it cannot be trusted as the search surface.
            // Derive the searchable fields from the server-declared columns()
            // method instead (mirrors GlobalSearch and the FilterHandler guard).
            collect($this->component->declaredColumns())
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

        $inspector = app(SchemaInspector::class);

        try {
            return $inspector->columnTypes($modelTable, $connection);
        } catch (Throwable) {
            return $inspector->columnListing($modelTable, $connection);
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
        return $this->component->applyBeforeSearch($field, $search);
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
