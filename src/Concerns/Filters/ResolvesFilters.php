<?php

namespace PowerComponents\LivewirePowerGrid\Concerns\Filters;

use Closure;
use PowerComponents\LivewirePowerGrid\Support\FilterWire;

trait ResolvesFilters
{
    protected function resolveFilters(): void
    {
        $declared = $this->declaredFilters();

        if ($declared === []) {
            return;
        }

        $columns = $this->columns;
        $columnsByField = $this->indexColumnsByField($columns);
        $panelPending = $this->usesFilterPanel() && ! $this->filterPanelLoaded;

        foreach ($declared as $filter) {
            $columnKey = data_get($filter, 'column');
            $targets = is_string($columnKey) ? ($columnsByField[$columnKey] ?? []) : [];

            if ($targets === []) {
                continue;
            }

            $definition = $panelPending && data_get($filter, 'dataSource') instanceof Closure
                ? $this->withoutServerOnlyKeys($filter, ['dataSource', 'builder', 'collection'])
                : $this->resolveFilterDefinition($filter);

            $this->registerDynamicFilterPaths($definition);

            foreach ($targets as $index) {
                $column = $columns[$index];
                data_set($column, 'filters', $this->filterForView($definition));
                $columns[$index] = $column;
            }
        }

        $this->columns = array_values($columns);

        if ($this->usesFilterInline()) {
            $this->inlineFiltersResolved = true;
        }
    }

    /**
     * @param  list<mixed>  $columns
     * @return array<string, list<int>>
     */
    protected function indexColumnsByField(array $columns): array
    {
        $index = [];

        foreach ($columns as $position => $column) {
            foreach ([data_get($column, 'field'), data_get($column, 'dataField')] as $key) {
                if (is_string($key) && $key !== '' && ! in_array($position, $index[$key] ?? [], true)) {
                    $index[$key][] = $position;
                }
            }
        }

        return $index;
    }

    /**
     * Resolve the filter's dataSource closure (once — the result is memoized on
     * the declared filter) and hand back a view-safe copy.
     */
    protected function resolveFilterDefinition(mixed $filter): mixed
    {
        if (data_get($filter, 'dataSource') instanceof Closure) {
            $depends = (array) data_get($filter, 'depends');

            if ($this->usesFilterInline() && ! $depends && $this->inlineFiltersResolved) {
                data_set($filter, 'dataSource', []);
            } else {
                /** @var Closure $closure */
                $closure = data_get($filter, 'dataSource');

                data_set($filter, 'dataSource', $closure($this->filterDependsValues($depends)));
            }
        }

        $definition = $this->withoutServerOnlyKeys($filter, ['builder', 'collection']);

        return is_object($definition) && method_exists($definition, 'execute')
            ? $definition->execute()
            : $definition;
    }

    /**
     * Current values of the filters this one depends on, keyed by field.
     *
     * @param  array<array-key, mixed>  $depends
     * @return array<array-key, mixed>
     */
    protected function filterDependsValues(array $depends): array
    {
        if (! $depends || ! $this->filters) {
            return $depends;
        }

        $values = [];

        foreach ($depends as $field) {
            if (! is_string($field)) {
                continue;
            }

            $record = $this->filters[$field] ?? null;
            $values[$field] = is_array($record) ? ($record['value'] ?? null) : null;
        }

        return $values;
    }

    /**
     * A view-safe copy: the server-only keys (closures, query objects) removed.
     *
     * @param  list<string>  $keys
     */
    protected function withoutServerOnlyKeys(mixed $filter, array $keys): mixed
    {
        $copy = is_object($filter) ? clone $filter : $filter;

        foreach ($keys as $key) {
            data_forget($copy, $key);
        }

        return $copy;
    }

    /**
     * FilterDynamic binds arbitrary `filters.*` paths; make sure each one exists
     * in the bag so Livewire can bind to it.
     */
    protected function registerDynamicFilterPaths(mixed $definition): void
    {
        if (data_get($definition, 'className') !== 'PowerComponents\Turbine\Components\Filters\FilterDynamic'
            || blank(data_get($definition, 'attributes'))) {
            return;
        }

        foreach ((array) data_get($definition, 'attributes') as $value) {
            if (! is_string($value) || ! str_contains($value, 'filters.')) {
                continue;
            }

            $path = (string) str($value)->after('filters.');

            if (is_null(data_get($this->filters, $path))) {
                $this->setInFilters($this->filters, $path, null);
            }
        }
    }

    /**
     * @return array<string, mixed>
     */
    protected function filterForView(mixed $filter): array
    {
        /** @var array<string, mixed> $definition */
        $definition = (array) $filter;

        return FilterWire::forView($definition, $this->usesFilterPanel(), $this->filterWire());
    }
}
