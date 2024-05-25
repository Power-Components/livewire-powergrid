<?php

namespace PowerComponents\LivewirePowerGrid\Concerns;

use DateTimeZone;
use Illuminate\Support\{Arr, Carbon, Collection};
use Livewire\Attributes\On;
use PowerComponents\LivewirePowerGrid\Column;

trait Filter
{
    public array $filters = [];

    public array $filtered = [];

    public array $enabledFilters = [];

    public array $select = [];

    public bool $showFilters = false;

    public function clearFilter(string $field = '', bool $emit = true): void
    {
        collect($this->filters())
            ->each(function ($filter) use ($field) {
                /**
                 * Filter Number will generate FIELD_start and FIELD_end fields,
                 * and both fields should also be cleaned.
                 * Here we verify if there are filter numbers and their fields.
                 */

                $extraFieldsToClear = [];

                if (!empty($this->filters['number'])) {
                    $numberField = str($field)->beforeLast('_start')->beforeLast('_end')->append('')->toString();

                    if (isset($this->filters['number'][$numberField])) {
                        $field              = $numberField;
                        $extraFieldsToClear = [$numberField . '_start', $numberField . '_end'];
                    }
                }

                if (isset($this->filters['multi_select'][$field])) {
                    $this->dispatch('pg:clear_multi_select::' . $this->tableName . ':' . $field);
                }

                if (isset($this->filters['datetime'][$field]) || isset($this->filters['date'][$field])) {
                    $this->dispatch('pg:clear_flatpickr::' . $this->tableName . ':' . $field);
                }

                $unset = function ($filter, $field, $column) {
                    $key = data_get($filter, 'key');

                    if (str($field)->contains('.')) {
                        $explodeField = explode('.', $field);

                        $currentArray = &$this->filters[$key];
                        unset($currentArray[$explodeField[0]]);
                    }

                    unset($this->filters[$key][$field]);

                    $this->enabledFilters = array_filter(
                        $this->enabledFilters,
                        fn ($filter) => $filter['field'] !== ($column ?? $field)
                    );
                };

                if ($field === data_get($filter, 'column')) {
                    $unset($filter, data_get($filter, 'field'), $field);
                }

                if ($field === data_get($filter, 'field')) {
                    $unset($filter, $field, null);

                    foreach ($extraFieldsToClear as $fieldToClear) {
                        $unset($filter, $fieldToClear, null);
                    }
                };
            });

        if ($emit) {
            $this->dispatch('pg:events', ['event' => 'clearFilters', 'field' => $field, 'tableName' => $this->tableName]);
        }

        $this->persistState('filters');
    }

    public function clearAllFilters(): void
    {
        $this->enabledFilters = [];
        $this->filters        = [];

        $this->persistState('filters');

        $this->dispatch('pg:clear_all_flatpickr::' . $this->tableName);
        $this->dispatch('pg:clear_all_multi_select::' . $this->tableName);
    }

    public function toggleFilters(): void
    {
        $this->showFilters = !$this->showFilters;
    }

    #[On('pg:datePicker-{tableName}')]
    public function datePickerChanged(
        array $selectedDates,
        string $field,
        string $dateStr,
        string $label,
        string $type,
        string $timezone = 'UTC',
    ): void {
        if (!isset($selectedDates[1])) {
            return;
        }

        $this->resetPage();

        $startDate = strval($selectedDates[0]);
        $endDate   = strval($selectedDates[1]);

        $appTimeZone = strval(config('app.timezone'));

        $filterTimezone = new DateTimeZone($timezone);

        $startDate = Carbon::parse($startDate)->format('Y-m-d');
        $endDate   = Carbon::parse($endDate)->format('Y-m-d');

        $startDate = Carbon::createFromFormat('Y-m-d', $startDate, $filterTimezone);
        $endDate   = Carbon::createFromFormat('Y-m-d', $endDate, $filterTimezone);

        if ($type === 'datetime') {
            $startDate->startOfDay()->setTimeZone($appTimeZone);
            $endDate->endOfDay()->setTimeZone($appTimeZone);
        }

        $this->addEnabledFilters($field, $label);

        $this->filters[$type][$field]['start'] = $startDate->toString();
        $this->filters[$type][$field]['end']   = $endDate->toString();

        $this->filters[$type][$field]['formatted'] = $dateStr;

        $this->persistState('filters');
    }

    #[On('pg:multiSelect-{tableName}')]
    public function multiSelectChanged(
        string $field,
        string $label,
        array $values,
    ): void {
        $this->resetPage();

        data_set($this->filters, "multi_select.$field", $values);

        $this->addEnabledFilters($field, $label);

        if (count($values) === 0) {
            $this->clearFilter($field, emit: false);
        }

        $this->afterChangedMultiSelectFilter($field, $values);

        $this->persistState('filters');
    }

    public function filterSelect(string $field, string $label): void
    {
        $this->resetPage();

        $this->addEnabledFilters($field, $label);

        $value = data_get($this->filters, "select.$field");

        if (blank($value)) {
            $this->clearFilter($field, emit: false);
        }

        $this->afterChangedSelectFilter($field, $label, $value);

        $this->persistState('filters');
    }

    public function filterNumberStart(string $field, array $params, string $value): void
    {
        extract($params);

        $this->resetPage();

        $this->addEnabledFilters($field, $title);

        if (blank($value)) {
            $this->clearFilter($field, emit: false);
        }

        $this->afterChangedNumberStartFilter($field, $title, $value);

        $this->persistState('filters');
    }

    public function filterNumberEnd(string $field, array $params, string $value): void
    {
        extract($params);

        $this->resetPage();

        $this->addEnabledFilters($field, $title);

        if (blank($value)) {
            $this->clearFilter($field, emit: false);
        }

        $this->afterChangedNumberEndFilter($field, $title, $value);

        $this->persistState('filters');
    }

    public function filterBoolean(string $field, string $value, string $label): void
    {
        $this->resetPage();

        $this->addEnabledFilters($field, $label);

        if ($value == 'all') {
            $this->clearFilter($field, emit: false);
        }

        $this->afterChangedBooleanFilter($field, $label, $value);

        $this->persistState('filters');
    }

    public function filterInputText(string $field, string $value, string $label = ''): void
    {
        $this->resetPage();

        $this->addEnabledFilters($field, $label);

        if (blank($value)) {
            $this->clearFilter($field, emit: false);
        }

        $this->afterChangedInputTextFilter($field, $label, $value);

        $this->persistState('filters');
    }

    public function filterInputTextOptions(string $field, string $value, string $label = ''): void
    {
        data_set($this->filters, 'input_text_options.' . $field, $value);

        $disabled = false;

        $this->resetPage();

        if (in_array($value, ['is_empty', 'is_not_empty', 'is_null', 'is_not_null', 'is_blank', 'is_not_blank'])) {
            $disabled = true;

            if (str($field)->contains('.')) {
                $this->filters['input_text'][str($field)->before('.')->toString()][str($field)->after('.')->toString()] = null;
            } else {
                $this->filters['input_text'][$field] = null;
            }
        }

        if (!collect($this->enabledFilters)->where('field', $field)->count()) {
            $this->enabledFilters[] = [
                'field'    => $field,
                'label'    => $label,
                'disabled' => $disabled,
            ];
        }

        if (blank($value)) {
            $this->clearFilter($field, emit: false);
        }

        $this->persistState('filters');
    }

    private function resolveFilters(): void
    {
        $filters = collect($this->filters());

        if ($filters->isEmpty()) {
            return;
        }

        $filters->each(function ($filter) {
            $this->columns = collect($this->columns)->map(function ($column) use ($filter) {
                if (data_get($column, 'field') === data_get($filter, 'column') ||
                    data_get($column, 'dataField') === data_get($filter, 'column')) {
                    if (data_get($filter, 'dataSource') instanceof \Closure) {
                        $depends = (array) data_get($filter, 'depends');
                        $closure = data_get($filter, 'dataSource');

                        if ($depends && $this->filters) {
                            $depends = collect($depends)
                                ->mapWithKeys(fn ($field) => [$field => data_get($this->filters, 'select', $field)]);
                        }

                        data_forget($filter, 'dataSource');
                        data_set($filter, 'dataSource', $closure($depends));
                    }

                    data_forget($filter, 'builder');
                    data_forget($filter, 'collection');

                    if (!is_array($filter) && method_exists($filter, 'execute')) {
                        $filter = $filter->execute();
                    }

                    data_set($column, 'filters', (array) $filter);

                    if (isset($this->filters[data_get($filter, 'field')])
                        && in_array(data_get($filter, 'field'), array_keys($this->filters[data_get($filter, 'key')]))
                        && array_values($this->filters[data_get($filter, 'key')])) {
                        $this->enabledFilters[] = [
                            'field' => data_get($filter, 'field'),
                            'label' => data_get($column, 'title'),
                        ];
                    }

                    if (data_get($filter, 'className') === 'PowerComponents\LivewirePowerGrid\Components\Filters\FilterDynamic' &&
                        filled(data_get($filter, 'attributes'))) {
                        $attributes = array_values((array) data_get($filter, 'attributes'));

                        foreach ($attributes as $value) {
                            if (is_string($value) && str_contains($value, 'filters.') && is_null(data_get($this->filters, str($value)->after('filters.')))) {
                                data_set($this->filters, str($value)->replace('filters.', ''), null);
                            }
                        }
                    }
                }

                return $column;
            })->toArray();
        });
    }

    public function addEnabledFilters(string $field, ?string $label): void
    {
        if (!collect($this->enabledFilters)
            ->where('field', $field)
            ->count()) {
            $this->enabledFilters[] = [
                'field' => $field,
                'label' => $label,
            ];
        }
    }

    public function listColumnForQueryString(): Collection
    {
        $columns = collect();

        collect($this->columns())
            ->ensure([Column::class])
            ->each(function ($column) use (&$columns) {
                if (isset($column->dataField)) {
                    $columns->put($column->dataField, $column->title ?? $column->dataField);
                }

                $columns->put($column->field, $column->title ?? $column->field);
            });

        return $columns;
    }

    /**
     *
     * @param string $prefix Prefix each field in URL
     */
    protected function powerGridQueryString(string $prefix = ''): array
    {
        $queryString = [];

        $columns = $this->listColumnForQueryString();

        foreach (Arr::dot($this->filters()) as $filter) {
            $as = str($filter->field)
                ->when(!empty($prefix), fn ($c) => $c->prepend($prefix . '_'))
                ->replace('.', '_')
                ->replaceMatches('/\_+/', '_');

            if (!empty(request()->get($as))) {
                $this->addEnabledFilters($filter->field, strval($columns->get($filter->field, $filter->field)));
            }

            if ($filter->key === 'input_text') {
                $queryString['filters.input_text.' . $filter->field] = [
                    'as'     => $as->toString(),
                    'except' => '',
                ];

                $queryString['filters.input_text_options.' . $filter->field] = [
                    'as'     => $as->append('_operator')->toString(),
                    'except' => '',
                ];

                continue;
            }

            if ($filter->key === 'number') {
                $_start         = $as->append('_start')->toString();
                $_end           = $as->append('_end')->toString();
                $fieldProcessed = false;

                $queryString['filters.number.' . $filter->field . '.start'] = [
                    'as'     => $_start,
                    'except' => '',
                ];

                if ($fieldProcessed === false && !is_null(request()->get($_start))) {
                    $this->addEnabledFilters($filter->field . '_start', strval($columns->get($filter->field, $filter->field)));

                    $fieldProcessed = true;
                }

                $queryString['filters.number.' . $filter->field . '.end'] = [
                    'as'     => $_end,
                    'except' => '',
                ];

                if ($fieldProcessed === false && !is_null(request()->get($_end))) {
                    $this->addEnabledFilters($filter->field . '_end', strval($columns->get($filter->field, $filter->field)));

                    $fieldProcessed = true;
                }

                continue;
            }

            if ($filter->key === 'dynamic') {
                $wireModel = array_values(
                    Arr::where(
                        (array) data_get($filter, 'attributes'),
                        fn ($value, $key) => str($key)->contains('wire:model')
                    )
                );

                if (count($wireModel)) {
                    $queryString[$wireModel[0]] = [
                        'as'     => $as->toString(),
                        'except' => '',
                    ];
                }

                continue;
            }

            $queryString['filters.' . $filter->key . '.' . $filter->field] = [
                'as'     => $as->toString(),
                'except' => '',
            ];
        }

        return $queryString;
    }
}
