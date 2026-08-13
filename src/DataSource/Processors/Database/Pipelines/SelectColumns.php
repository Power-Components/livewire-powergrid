<?php

namespace PowerComponents\LivewirePowerGrid\DataSource\Processors\Database\Pipelines;

use Closure;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\{Builder as EloquentBuilder, Model};
use Illuminate\Database\Query\Builder as QueryBuilder;
use PowerComponents\LivewirePowerGrid\Contracts\{PowerGridContext, SchemaInspector};
use Throwable;

class SelectColumns
{
    public function __construct(
        protected PowerGridContext $component,
        protected bool $isExport = false
    ) {}

    public function handle(mixed $query, Closure $next): mixed
    {
        if (! $this->shouldPrune($query)) {
            return $next($query);
        }

        /** @var EloquentBuilder<Model>|QueryBuilder $query */
        $baseQuery = $query instanceof EloquentBuilder ? $query->getQuery() : $query;

        if (filled($baseQuery->columns) || filled($baseQuery->joins)) {
            return $next($query);
        }

        $excluded = $this->excludedFields();

        if (empty($excluded)) {
            return $next($query);
        }

        $allColumns = $this->tableColumns($query);

        if (empty($allColumns)) {
            return $next($query);
        }

        $select = array_values(array_diff($allColumns, $excluded));

        if (empty($select) || count($select) === count($allColumns)) {
            return $next($query);
        }

        $primaryKey = $this->component->state()->primaryKey;

        if (! in_array($primaryKey, $select, true) && in_array($primaryKey, $allColumns, true)) {
            $select[] = $primaryKey;
        }

        $query->select($select);

        return $next($query);
    }

    protected function shouldPrune(mixed $query): bool
    {
        if ($this->isExport || ! $this->component->state()->pruneHiddenColumns) {
            return false;
        }

        return $query instanceof EloquentBuilder || $query instanceof QueryBuilder;
    }

    /**
     * dataFields of hidden+searchable columns, restricted to plain base-table
     * columns that are not referenced by any visible column.
     *
     * @return list<string>
     */
    protected function excludedFields(): array
    {
        $columns = collect($this->component->state()->columns);

        $reservedByVisible = [];

        foreach ($columns as $column) {
            if ((bool) data_get($column, 'forceHidden')) {
                continue;
            }

            foreach (['dataField', 'field', 'contentClassField'] as $key) {
                $value = $this->stringValue(data_get($column, $key));

                if ($value !== '') {
                    $reservedByVisible[$value] = true;
                }
            }
        }

        $excluded = [];

        foreach ($columns as $column) {
            if (! (bool) data_get($column, 'hidden') || ! (bool) data_get($column, 'searchable')) {
                continue;
            }

            $field = $this->stringValue(data_get($column, 'dataField')) ?: $this->stringValue(data_get($column, 'field'));

            if ($field === '' || str_contains($field, '.') || isset($reservedByVisible[$field])) {
                continue;
            }

            $excluded[$field] = true;
        }

        return array_keys($excluded);
    }

    protected function stringValue(mixed $value): string
    {
        return is_string($value) ? $value : '';
    }

    /**
     * All columns of the base table, from the shared schema cache.
     *
     * @param  EloquentBuilder<Model>|QueryBuilder  $query
     * @return list<string>
     */
    protected function tableColumns(EloquentBuilder|QueryBuilder $query): array
    {
        $table = $this->component->getCurrentTable();

        if ($table === '') {
            return [];
        }

        $conn = $query instanceof EloquentBuilder
            ? $query->getModel()->getConnection()
            : $query->getConnection();
        $connection = $conn instanceof Connection ? $conn->getName() : null;

        $inspector = app(SchemaInspector::class);

        try {
            $cached = $inspector->columnTypes($table, $connection);

            return array_is_list($cached) ? $cached : array_keys($cached);
        } catch (Throwable) {
            try {
                return $inspector->columnListing($table, $connection);
            } catch (Throwable) {
                return [];
            }
        }
    }
}
