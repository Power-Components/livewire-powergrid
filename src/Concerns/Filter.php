<?php

namespace PowerComponents\LivewirePowerGrid\Concerns;

use Closure;
use Exception;
use Illuminate\Support\{Arr, Collection};
use Livewire\Attributes\On;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Plugins\Flatpickr\FlatpickrPlugin;
use PowerComponents\Turbine\Components\Filters\{FilterBase, FilterManager};

trait Filter
{
    /** @var array<string, array<string, mixed>> */
    public array $filters = [];

    /**
     * @var array<string, array<string, mixed>>
     */
    public array $draftFilters = [];

    /** @var list<int|string> */
    public array $filtered = [];

    /** @var list<array<string, mixed>> */
    public array $enabledFilters = [];

    /** @var array<string, mixed> */
    public array $select = [];

    public bool $showFilters = false;

    public bool $emitClearFiltersEvent = true;

    public function emitClearFiltersEvent(bool $emit): void
    {
        $this->emitClearFiltersEvent = $emit;
    }

    /** @param array<string, array<string, mixed>> $target */
    private function setInFilters(array &$target, string $key, mixed $value): void
    {
        /** @phpstan-ignore parameterByRef.type */
        data_set($target, $key, $value);
    }

    protected function applyDefaultFilters(): void
    {
        $filterManager = new FilterManager();
        $applied = $filterManager->applyDefaults(
            declaredFilters: $this->filters(),
            columns: $this->columns,
            /** @phpstan-ignore assign.propertyType */
            filters: $this->filters,
            enabledFilters: $this->enabledFilters
        );

        if ($applied) {
            $this->persistState('filters');
        }
    }

    /**
     * @throws Exception
     */
    public function clearFilter(string $field = ''): void
    {
        collect($this->filters())
            ->each(function ($filter) use ($field) {
                $extraFieldsToClear = [];

                if (isset($this->filters['datetime']) || isset($this->filters['date'])) {
                    $this->dispatch('pg:clear_flatpickr::'.$this->tableName.':'.$field);
                }

                if (! empty($this->filters['number'])) {
                    $numberField = str($field)->beforeLast('_start')->beforeLast('_end')->append('')->toString();

                    if (isset($this->filters['number'][$numberField])) {
                        $field = $numberField;
                        $extraFieldsToClear = [$numberField.'_start', $numberField.'_end'];
                    }
                }

                // Because multi_select filters can be nested
                // We need to use data_get to access the field
                // Example of field: user.roles would not be accessible with $this->filters['multi_select'][$field] since it is nested as
                // $this->filters['multi_select']['user']['roles']
                // By using data_get, we can access it regardless of nesting
                // This is needed because in the slimSelect.js the dataField is set as 'multi_select.user.roles'
                if (data_get($this->filters, "multi_select.$field")) {
                    $this->dispatch('pg:clear_multi_select::'.$this->tableName.':'.$field);
                }

                if (isset($this->filters['datetime'][$field]) || isset($this->filters['date'][$field])) {
                    $this->dispatch('pg:clear_flatpickr::'.$this->tableName.':'.$field);
                }

                $unset = function ($filter, $field, $column) {
                    /** @var string $key */
                    $key = data_get($filter, 'key');

                    if (str($field)->contains('.')) {
                        $explodeField = explode('.', $field);

                        $currentArray = &$this->filters[$key];

                        $this->removeNestedArrayKey($currentArray, $explodeField[0], $explodeField[1]);
                    }

                    unset($this->filters[$key][$field]);

                    if (empty($this->filters[$key])) {
                        unset($this->filters[$key]);
                    }

                    $this->enabledFilters = array_values(array_filter(
                        $this->enabledFilters,
                        fn ($filter) => $filter['field'] !== ($column ?? $field)
                    ));
                };

                if ($field === data_get($filter, 'column')) {
                    $unset($filter, data_get($filter, 'field'), $field);
                }

                if ($field === data_get($filter, 'field')) {
                    $unset($filter, $field, null);

                    foreach ($extraFieldsToClear as $fieldToClear) {
                        $unset($filter, $fieldToClear, null);
                    }
                }
            });

        if ($this->emitClearFiltersEvent) {
            $this->dispatch('pg:events', ['event' => 'clearFilters', 'field' => $field, 'tableName' => $this->tableName]);
        }

        $this->persistState('filters');

        $this->renderOutsideFiltersPartial();
    }

    /**
     * @throws Exception
     */
    public function clearAllFilters(): void
    {
        $this->enabledFilters = [];
        $this->filters = [];
        $this->draftFilters = [];
        $this->filterBuilder = ['match' => 'and', 'rows' => []];

        $this->resetPage();

        $this->persistState('filters');

        $this->dispatch('pg:clear_all_flatpickr::'.$this->tableName);
        $this->dispatch('pg:clear_all_multi_select::'.$this->tableName);

        $this->renderOutsideFiltersPartial();
    }

    /**
     * @throws Exception
     */
    public function applyFilters(): void
    {
        /** @var array<string, mixed> $draft */
        $draft = $this->draftFilters;

        foreach (['date', 'datetime'] as $dateKey) {
            /** @var array<string, mixed> $entries */
            $entries = (array) data_get($draft, $dateKey, []);

            foreach ($entries as $field => $value) {
                $formatted = data_get($value, 'formatted');

                if (blank($formatted)) {
                    unset($entries[$field]);

                    continue;
                }

                $entries[$field] = FlatpickrPlugin::computeRange($dateKey, is_scalar($formatted) ? (string) $formatted : '');
            }

            $draft[$dateKey] = $entries;

            if (empty($draft[$dateKey])) {
                unset($draft[$dateKey]);
            }
        }

        /** @var array<string, mixed> $operators */
        $operators = (array) data_get($draft, 'input_text_options', []);

        foreach ($operators as $field => $operator) {
            if (in_array($operator, ['is_empty', 'is_not_empty', 'is_null', 'is_not_null', 'is_blank', 'is_not_blank'], true)) {
                data_set($draft, 'input_text.'.$field, null);
            }
        }

        /** @var array<string, mixed> $draft */
        $filters = $this->pruneBlankFilters($draft);

        $this->filters = $filters;
        $this->draftFilters = $filters;

        $this->rebuildEnabledFilters();
        $this->syncFilterBuilderPills();

        $this->resetPage();
        $this->persistState('filters');
        $this->renderOutsideFiltersPartial();
    }

    public function resetFilters(): void
    {
        $this->draftFilters = $this->filters;

        $this->dispatch('pg:restore_flatpickr::'.$this->tableName);
        $this->dispatch('pg:restore_multi_select::'.$this->tableName);

        $this->renderOutsideFiltersPartial();
    }

    public function activeFilterCount(): int
    {
        return collect($this->enabledFilters)
            ->reject(fn ($filter) => ($filter['source'] ?? null) === 'filterBuilder')
            ->map(function ($filter) {
                $field = data_get($filter, 'field');
                $field = is_string($field) ? $field : '';

                return (string) str($field)->beforeLast('_start')->beforeLast('_end');
            })
            ->filter(fn ($field) => $field !== '')
            ->unique()
            ->count();
    }

    private function rebuildEnabledFilters(): void
    {
        $this->enabledFilters = [];

        $titles = [];

        foreach ($this->columns as $column) {
            $titleValue = data_get($column, 'title');
            $title = is_scalar($titleValue) ? (string) $titleValue : '';

            if (($field = data_get($column, 'field')) && is_string($field)) {
                $titles[$field] = $title;
            }

            if (($dataField = data_get($column, 'dataField')) && is_string($dataField)) {
                $titles[$dataField] = $title;
            }
        }

        foreach ($this->filters() as $filter) {
            $key = data_get($filter, 'key');
            $key = is_string($key) ? $key : '';
            $field = data_get($filter, 'field');
            $field = is_string($field) ? $field : '';
            $column = data_get($filter, 'column');
            $column = is_string($column) ? $column : '';

            $title = $titles[$column] ?? $titles[$field] ?? $field;

            if ($key === 'number') {
                $range = data_get($this->filters, 'number.'.$field);

                if (filled(data_get($range, 'start')) || filled(data_get($range, 'end'))) {
                    $this->addEnabledFilters($field, $title);
                }

                continue;
            }

            if (filled(data_get($this->filters, $key.'.'.$field))) {
                $this->addEnabledFilters($field, $title);
            }
        }

        // Valueless operators produce an enabled (value-less) filter of their own.
        /** @var array<string, mixed> $operators */
        $operators = (array) data_get($this->filters, 'input_text_options', []);

        foreach ($operators as $field => $operator) {
            if (in_array($operator, ['is_empty', 'is_not_empty', 'is_null', 'is_not_null', 'is_blank', 'is_not_blank'], true)) {
                $this->addEnabledFilters(strval($field), $titles[$field] ?? strval($field));
            }
        }
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, array<string, mixed>>
     */
    private function pruneBlankFilters(array $filters): array
    {
        foreach ($filters as $key => $values) {
            if (! is_array($values)) {
                if (blank($values)) {
                    unset($filters[$key]);
                }

                continue;
            }

            foreach ($values as $field => $value) {
                if (! is_array($value) && blank($value)) {
                    unset($values[$field]);
                }

                if (is_array($value) && $key === 'multi_select' && blank(array_filter($value, fn ($v) => filled($v)))) {
                    unset($values[$field]);
                }
            }

            if (empty($values)) {
                unset($filters[$key]);

                continue;
            }

            $filters[$key] = $values;
        }

        /** @var array<string, array<string, mixed>> $filters */
        return $filters;
    }

    public function toggleFilters(): void
    {
        $this->showFilters = ! $this->showFilters;

        $this->renderOutsideFiltersPartial();
    }

    public function updatedShowFilters(): void
    {
        $this->renderOutsideFiltersPartial();
    }

    public function filterPosition(): string
    {
        $position = config('livewire-powergrid.filter');

        return is_string($position) ? $position : '';
    }

    /** True when filters render inline, inside the table header row. */
    public function usesFilterInline(): bool
    {
        return $this->filterPosition() === 'inline';
    }

    /** True when filters live in their own panel instead of inside the table. */
    public function usesFilterPanel(): bool
    {
        return in_array($this->filterPosition(), ['dropdown', 'flyout'], true);
    }

    public function usesFilterDropdown(): bool
    {
        return $this->filterPosition() === 'dropdown';
    }

    public function usesFilterFlyout(): bool
    {
        return $this->filterPosition() === 'flyout';
    }

    public function filterPanelView(): string
    {
        return $this->filterPosition() === 'flyout' ? 'filter.flyout' : 'filter.dropdown';
    }

    public function filterPanelColumns(): int
    {
        $count = count($this->filters());

        return match (true) {
            $count > 6 => 3,
            $count > 4 => 2,
            default => 1,
        };
    }

    /**
     * Filter-bearing columns for dropdown/flyout panels.
     * Uses Filter::order() when set, otherwise the filters() array index.
     *
     * @param  iterable<mixed>|null  $columns
     * @return Collection<int, mixed>
     */
    public function sortedFilterPanelColumns(?iterable $columns = null): Collection
    {
        $source = collect($columns ?? [])
            ->filter(fn ($column) => filled(data_get($column, 'filters')));

        if ($source->isEmpty()) {
            $source = collect($this->columns)
                ->filter(fn ($column) => filled(data_get($column, 'filters')));
        }

        $declarationOrder = collect($this->filters())
            ->values()
            ->mapWithKeys(function ($filter, int $index): array {
                $field = data_get($filter, 'field');
                $key = is_string($field) || is_numeric($field) ? (string) $field : (string) $index;

                return [$key => $index];
            });

        return $source
            ->sortBy(function ($column) use ($declarationOrder): string {
                $declared = $declarationOrder->get((string) data_get($column, 'filters.field'), PHP_INT_MAX);
                $explicit = data_get($column, 'filters.order');
                $order = is_numeric($explicit) ? (int) $explicit : $declared;

                return sprintf('%010d-%010d', $order, $declared);
            })
            ->values();
    }

    /**
     * @return array{position: string, close_on_escape: bool, close_on_click_outside: bool}
     */
    public function filterFlyoutOptions(): array
    {
        $position = config('livewire-powergrid.filter_flyout.position', 'right');

        return [
            'position' => in_array($position, ['left', 'right'], true) ? $position : 'right',
            'close_on_escape' => boolval(config('livewire-powergrid.filter_flyout.close_on_escape', true)),
            'close_on_click_outside' => boolval(config('livewire-powergrid.filter_flyout.close_on_click_outside', true)),
        ];
    }

    /**
     * @throws Exception
     */
    /**
     * @param  list<string>  $values
     *
     * @throws Exception
     */
    #[On('pg:multiSelect-{tableName}')]
    public function multiSelectChanged(
        string $field,
        string $label,
        array $values,
    ): void {
        $this->resetPage();

        $this->setInFilters($this->filters, "multi_select.$field", $values);

        $this->addEnabledFilters($field, $label);

        if (count($values) === 0) {
            $this->clearFilter($field);
        }

        $this->afterChangedMultiSelectFilter($field, $values);

        $this->persistState('filters');

        $this->renderOutsideFiltersPartial();
    }

    /**
     * @throws Exception
     */
    public function filterSelect(string $field, string $label): void
    {
        $this->resetPage();

        $this->addEnabledFilters($field, $label);

        $value = data_get($this->filters, "select.$field");

        if (blank($value)) {
            $this->clearFilter($field);
        }

        $this->afterChangedSelectFilter($field, $label, $value);

        $this->persistState('filters');

        $this->renderOutsideFiltersPartial();
    }

    /**
     * @param  array<string, mixed>  $params
     *
     * @throws Exception
     */
    public function filterNumberStart(string $field, array $params, string $value): void
    {
        /** @var string $title */
        $title = data_get($params, 'title');

        $this->resetPage();

        $this->addEnabledFilters($field, $title);

        if (blank($value)) {
            $this->clearFilter($field);
        }

        $this->afterChangedNumberStartFilter($field, $title, $value);

        $this->persistState('filters');

        $this->renderOutsideFiltersPartial();
    }

    /**
     * @param  array<string, mixed>  $params
     *
     * @throws Exception
     */
    public function filterNumberEnd(string $field, array $params, string $value): void
    {
        /** @var string $title */
        $title = data_get($params, 'title');

        $this->resetPage();

        $this->addEnabledFilters($field, $title);

        if (blank($value)) {
            $this->clearFilter($field);
        }

        $this->afterChangedNumberEndFilter($field, $title, $value);

        $this->persistState('filters');

        $this->renderOutsideFiltersPartial();
    }

    /**
     * @throws Exception
     */
    public function filterBoolean(string $field, string $value, string $label): void
    {
        $this->resetPage();

        $this->addEnabledFilters($field, $label);

        if ($value == 'all') {
            $this->clearFilter($field);
        }

        $this->afterChangedBooleanFilter($field, $label, $value);

        $this->persistState('filters');

        $this->renderOutsideFiltersPartial();
    }

    /**
     * @throws Exception
     */
    public function filterInputText(string $field, string $value, string $label = ''): void
    {
        $this->resetPage();

        $this->addEnabledFilters($field, $label);

        if (blank($value)) {
            $this->clearFilter($field);
        }

        $this->afterChangedInputTextFilter($field, $label, $value);

        $this->persistState('filters');

        $this->renderOutsideFiltersPartial();
    }

    /**
     * @throws Exception
     */
    public function filterInputTextOptions(string $field, string $value, string $label = ''): void
    {
        if (! $this->isDeclaredFilterField($field)) {
            return;
        }

        $this->setInFilters($this->filters, 'input_text_options.'.$field, $value);

        $disabled = false;

        $this->resetPage();

        if (in_array($value, ['is_empty', 'is_not_empty', 'is_null', 'is_not_null', 'is_blank', 'is_not_blank'])) {
            $disabled = true;

            if (str($field)->contains('.')) {
                $this->setInFilters($this->filters, 'input_text.'.str($field)->before('.').'.'.str($field)->after('.'), null);
            } else {
                $this->setInFilters($this->filters, 'input_text.'.$field, null);
            }
        }

        if (! collect($this->enabledFilters)->where('field', $field)->count()) {
            $this->enabledFilters[] = [
                'field' => $field,
                'label' => $label,
                'disabled' => $disabled,
            ];
        }

        if (blank($value)) {
            $this->clearFilter($field);
        }
        $this->persistState('filters');

        $this->renderOutsideFiltersPartial();
    }

    public function renderOutsideFiltersPartial(): void
    {
        if (! function_exists('partials')) {
            return;
        }

        $enabledFilters = $this->enabledFilters;
        $this->resolveFilters();
        $this->enabledFilters = $enabledFilters;

        partials($this)
            ->partial("pg-enabled-filters-{$this->tableName}", theme_view('header.enabled-filters'), [
                'enabledFilters' => $this->enabledFilters,
            ]);

        if (! isset($this->setUp['detail'])) {
            $this->renderGridPartials();
        }

        if (! $this->usesFilterPanel()) {
            return;
        }

        if ($this->usesFilterFlyout()) {
            partials($this)
                ->partial("pg-filter-trigger-{$this->tableName}", theme_view('header.filters'));
        }

        if (isset($this->setUp['detail'])) {
            return;
        }

        partials($this)
            ->partial("pg-filters-{$this->tableName}", theme_view($this->filterPanelView()));
    }

    protected function resolveFilters(): void
    {
        $filters = collect($this->filters());

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
                        $depends = (array) data_get($filter, 'depends');
                        $closure = data_get($filter, 'dataSource');

                        if ($depends && $this->filters) {
                            $depends = collect($depends)
                                ->mapWithKeys(function ($field) {
                                    /** @var string $field */
                                    return [$field => data_get($this->filters, 'select.'.$field)];
                                });
                        }

                        data_forget($filter, 'dataSource');
                        data_set($filter, 'dataSource', $closure($depends));
                    }

                    data_forget($filter, 'builder');
                    data_forget($filter, 'collection');

                    /** @var object|string $filter */
                    if (! is_array($filter) && method_exists($filter, 'execute')) {
                        $filter = $filter->execute();
                    }

                    data_set($column, 'filters', (array) $filter);

                    /** @var string $filterField */
                    $filterField = data_get($filter, 'field');
                    /** @var string $filterKey */
                    $filterKey = data_get($filter, 'key');

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

                    if (data_get($filter, 'className') === 'PowerComponents\Turbine\Components\Filters\FilterDynamic' &&
                        filled(data_get($filter, 'attributes'))) {
                        $attributes = array_values((array) data_get($filter, 'attributes'));

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
    }

    public function addEnabledFilters(string $field, ?string $label): void
    {
        if (! $this->isDeclaredFilterField($field)) {
            return;
        }

        if (! collect($this->enabledFilters)
            ->where('field', $field)
            ->count()) {
            $this->enabledFilters[] = [
                'field' => $field,
                'label' => $label ?? '',
            ];
        }
    }

    private function isDeclaredFilterField(string $field): bool
    {
        $declared = collect($this->declaredFilters())
            ->flatMap(function ($filter) {
                $fields = [
                    data_get($filter, 'field'),
                    data_get($filter, 'column'),
                ];

                /** @var string|null $filterField */
                $filterField = data_get($filter, 'field');

                if (is_string($filterField) && data_get($filter, 'key') === 'number') {
                    $fields[] = $filterField.'_start';
                    $fields[] = $filterField.'_end';
                }

                return $fields;
            })
            ->filter(fn ($value) => is_string($value) && $value !== '');

        return $declared->contains($field);
    }

    /** @return Collection<string, string> */
    public function listColumnForQueryString(): Collection
    {
        $columns = collect();

        collect($this->declaredColumns())
            ->ensure([Column::class])
            ->each(function ($column) use (&$columns) {
                if (filled($column->dataField)) {
                    $columns->put($column->dataField, $column->title ?: $column->dataField);
                }

                $columns->put($column->field, $column->title ?: $column->field);
            });

        return $columns;
    }

    /**
     * @param  string  $prefix  Prefix each field in URL
     * @return array<string, array{as: string, except: string}>
     */
    protected function powerGridQueryString(string $prefix = ''): array
    {
        $queryString = [];

        $columns = $this->listColumnForQueryString();

        foreach (Arr::dot($this->filters()) as $filter) {
            /** @var FilterBase $filter */
            /** @var string $field */
            $field = $filter->field;
            $as = str($field)
                ->when(filled($prefix), fn ($c) => $c->prepend($prefix.'_'))
                ->replace('.', '_')
                ->replaceMatches('/\_+/', '_');

            if (filled(request()->get($as))) {
                $this->addEnabledFilters($field, strval($columns->get($field, $field)));
            }

            /** @var string $key */
            $key = data_get($filter, 'key');

            if ($key === 'input_text') {
                $queryString['filters.input_text.'.$field] = [
                    'as' => $as->toString(),
                    'except' => '',
                ];

                $queryString['filters.input_text_options.'.$field] = [
                    'as' => $as->append('_operator')->toString(),
                    'except' => '',
                ];

                continue;
            }

            if ($key === 'number') {
                $_start = $as->append('_start')->toString();
                $_end = $as->append('_end')->toString();
                $fieldProcessed = false;

                $queryString['filters.number.'.$field.'.start'] = [
                    'as' => $_start,
                    'except' => '',
                ];

                if (filled(request()->get($_start))) {
                    $this->addEnabledFilters($field.'_start', strval($columns->get($field, $field)));

                    $fieldProcessed = true;
                }

                $queryString['filters.number.'.$field.'.end'] = [
                    'as' => $_end,
                    'except' => '',
                ];

                if ($fieldProcessed === false && filled(request()->get($_end))) {
                    $this->addEnabledFilters($field.'_end', strval($columns->get($field, $field)));
                }

                continue;
            }

            if ($key === 'dynamic') {
                $wireModel = array_values(
                    Arr::where(
                        (array) data_get($filter, 'attributes'),
                        fn ($value, $key) => str($key)->contains('wire:model')
                    )
                );

                if (count($wireModel) && is_string($wireModel[0])) {
                    $queryString[$wireModel[0]] = [
                        'as' => $as->toString(),
                        'except' => '',
                    ];
                }

                continue;
            }

            $queryString['filters.'.$key.'.'.$field] = [
                'as' => $as->toString(),
                'except' => '',
            ];
        }

        return $queryString;
    }

    /** @param  array<string, mixed>  $array */
    private function removeNestedArrayKey(array &$array, string $parent, string $child): void
    {
        if (isset($array[$parent]) && is_array($array[$parent])) {
            /** @var array<string, mixed> $nested */
            $nested = &$array[$parent];

            if (isset($nested[$child])) {
                unset($nested[$child]);
            }

            if (empty($array[$parent])) {
                unset($array[$parent]);
            }
        }
    }
}
