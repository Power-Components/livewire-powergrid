<?php

namespace PowerComponents\LivewirePowerGrid\Concerns;

use Closure;
use Exception;
use Illuminate\Support\{Arr, Collection};
use Livewire\Attributes\On;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\Turbine\Components\Filters\FilterBase;

trait Filter
{
    /** @var array<string, array<string, mixed>> */
    public array $filters = [];

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
        $columnsByField = collect($this->columns)
            ->mapWithKeys(function ($column) {
                /** @var Column $column */

                return [
                    filled($column->field) ? $column->field : $column->dataField => $column,
                ];
            });

        collect($this->filters())
            ->filter(fn ($filter) => filled($filter->defaultValue))
            ->each(function (FilterBase $filter) use (&$defaultFiltersApplied, $columnsByField) {
                /** @var string $field */
                $field = $filter->field;
                $column = $filter->column;

                /** @var string $key */
                $key = data_get($filter, 'key');
                $defaultValue = $filter->defaultValue;

                $columnData = $columnsByField->get($column);
                /** @var string|null $label */
                $label = data_get($columnData, 'title', $field);

                switch ($key) {
                    case 'select':
                        $this->setInFilters($this->filters, "select.{$field}", $defaultValue);
                        $this->addEnabledFilters($field, $label);
                        $defaultFiltersApplied = true;
                        break;

                    case 'multi_select':
                        $values = is_array($defaultValue) ? $defaultValue : [$defaultValue];
                        $this->setInFilters($this->filters, "multi_select.{$field}", $values);
                        $this->addEnabledFilters($field, $label);
                        $defaultFiltersApplied = true;
                        break;

                    case 'boolean':
                        $this->setInFilters($this->filters, "boolean.{$field}", $defaultValue);
                        $this->addEnabledFilters($field, $label);
                        $defaultFiltersApplied = true;
                        break;

                    case 'input_text':
                        if (is_array($defaultValue)) {
                            // Support for both value and operator
                            $this->setInFilters($this->filters, "input_text.{$field}", $defaultValue['value'] ?? '');
                            if (isset($defaultValue['operator'])) {
                                $this->setInFilters($this->filters, "input_text_options.{$field}", $defaultValue['operator']);
                            }
                        } else {
                            $this->setInFilters($this->filters, "input_text.{$field}", $defaultValue);
                        }
                        $this->addEnabledFilters($field, $label);
                        $defaultFiltersApplied = true;
                        break;

                    case 'number':
                        if (is_array($defaultValue)) {
                            if (isset($defaultValue['start'])) {
                                $this->setInFilters($this->filters, "number.{$field}.start", $defaultValue['start']);
                            }
                            if (isset($defaultValue['end'])) {
                                $this->setInFilters($this->filters, "number.{$field}.end", $defaultValue['end']);
                            }
                        } else {
                            $this->setInFilters($this->filters, "number.{$field}.start", $defaultValue);
                        }
                        $this->addEnabledFilters($field, $label);
                        $defaultFiltersApplied = true;
                        break;

                    case 'date':
                    case 'datetime':
                        if (is_array($defaultValue) && isset($defaultValue['start']) && isset($defaultValue['end'])) {
                            $this->filters[$key][$field] = [
                                'start' => $defaultValue['start'],
                                'end' => $defaultValue['end'],
                                'formatted' => $defaultValue['formatted'] ?? '',
                            ];
                            $this->addEnabledFilters($field, $label);
                            $defaultFiltersApplied = true;
                        }
                        break;
                }
            });

        if ($defaultFiltersApplied) {
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
        $this->filterBuilder = ['match' => 'and', 'rows' => []];

        $this->persistState('filters');

        $this->dispatch('pg:clear_all_flatpickr::'.$this->tableName);
        $this->dispatch('pg:clear_all_multi_select::'.$this->tableName);

        $this->renderOutsideFiltersPartial();
    }

    public function toggleFilters(): void
    {
        $this->showFilters = ! $this->showFilters;

        $this->renderOutsideFiltersPartial();
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

        if (config('livewire-powergrid.filter') !== 'outside') {
            return;
        }

        if (isset($this->setUp['detail'])) {
            return;
        }

        partials($this)
            ->partial("pg-tbody-{$this->tableName}", 'livewire-powergrid::components.partials.tbody')
            ->partial("pg-pagination-{$this->tableName}", theme_view('footer'))
            ->partial("pg-filters-{$this->tableName}", theme_view('filter'));
    }

    private function resolveFilters(): void
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
