<?php

namespace PowerComponents\LivewirePowerGrid\Concerns\Filters;

use Closure;
use PowerComponents\LivewirePowerGrid\Column;

trait ResolvesFilters
{
    protected function resolveFiltersForRender(): void
    {
        $enabledFilters = $this->enabledFilters;
        $this->resolveFilters();
        $this->enabledFilters = $enabledFilters;
    }

    protected function resolveFilters(): void
    {
        $filters = collect($this->declaredFilters());

        if ($filters->isEmpty()) {
            return;
        }

        $columns = $this->columns;

        $filters->each(function ($filter) use (&$columns) {
            foreach ($columns as $index => $column) {
                /** @var Column $column */
                if (data_get($column, 'field') === data_get($filter, 'column') ||
                    data_get($column, 'dataField') === data_get($filter, 'column')) {
                    if (data_get($filter, 'dataSource') instanceof Closure) {
                        if ($this->usesFilterPanel() && ! $this->filterPanelLoaded) {
                            $pending = is_object($filter) ? clone $filter : $filter;
                            data_forget($pending, 'dataSource');
                            data_forget($pending, 'builder');
                            data_forget($pending, 'collection');
                            data_set($column, 'filters', (array) $pending);
                            $columns[$index] = $column;

                            continue;
                        }

                        $depends = (array) data_get($filter, 'depends');

                        if ($this->usesFilterInline() && ! $depends && $this->inlineFiltersResolved) {
                            data_set($filter, 'dataSource', []);
                        } else {
                            $closure = data_get($filter, 'dataSource');

                            if ($depends && $this->filters) {
                                $depends = collect($depends)
                                    ->mapWithKeys(function ($field) {
                                        /** @var string $field */
                                        return [$field => data_get($this->filters, 'select.'.$field)];
                                    });
                            }

                            data_set($filter, 'dataSource', $closure($depends));
                        }
                    }

                    $columnFilter = is_object($filter) ? clone $filter : $filter;
                    data_forget($columnFilter, 'builder');
                    data_forget($columnFilter, 'collection');

                    /** @var object|string $columnFilter */
                    if (! is_array($columnFilter) && method_exists($columnFilter, 'execute')) {
                        $columnFilter = $columnFilter->execute();
                    }

                    data_set($column, 'filters', (array) $columnFilter);

                    /** @var string $filterField */
                    $filterField = data_get($columnFilter, 'field');
                    /** @var string $filterKey */
                    $filterKey = data_get($columnFilter, 'key');

                    if (isset($this->filters[$filterField])
                        && in_array($filterField, array_keys($this->filters[$filterKey]))
                        && array_values($this->filters[$filterKey])) {
                        /** @var string $labelValue */
                        $labelValue = data_get($column, 'title');
                        $this->enabledFilters[] = [
                            'field' => $filterField,
                            'label' => strval($labelValue),
                        ];
                    }

                    if (data_get($columnFilter, 'className') === 'PowerComponents\Turbine\Components\Filters\FilterDynamic' &&
                        filled(data_get($columnFilter, 'attributes'))) {
                        $attributes = array_values((array) data_get($columnFilter, 'attributes'));

                        foreach ($attributes as $value) {
                            if (is_string($value) && str_contains($value, 'filters.') && is_null(data_get($this->filters, str($value)->after('filters.')))) {
                                $this->setInFilters($this->filters, (string) str($value)->replace('filters.', ''), null);
                            }
                        }
                    }

                    $columns[$index] = $column;
                }
            }
        });

        $this->columns = $columns;

        if ($this->usesFilterInline()) {
            $this->inlineFiltersResolved = true;
        }
    }
}
