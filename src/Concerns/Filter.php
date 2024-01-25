<?php

namespace PowerComponents\LivewirePowerGrid\Concerns;

use DateTimeZone;
use Illuminate\Support\Carbon;
use Livewire\Attributes\On;

use function Livewire\store;

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
        if (str_contains($field, '.')) {
            list($table, $column) = explode('.', $field);
        } else {
            $table  = $field;
            $column = null;
        }

        $this->clearFilterByTableAndColumn($table, $column);

        unset($this->enabledFilters[$field]);

        if ($emit) {
            $this->dispatch('pg:events', ['event' => 'clearFilters', 'field' => $field, 'tableName' => $this->tableName]);
        }

        $this->persistState('filters');
    }

    public function toggleFilters(): void
    {
        $this->showFilters = !$this->showFilters;
    }

    public function clearAllFilters(): void
    {
        $this->enabledFilters = [];
        $this->filters        = [];

        $this->persistState('filters');

        $this->dispatch('pg:clear_all_flatpickr::' . $this->tableName);
        $this->dispatch('pg:clear_all_multi_select::' . $this->tableName);
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

        $this->enabledFilters[$field]['data-field'] = $field;
        $this->enabledFilters[$field]['label']      = $label;

        $this->filters[$type][$field]['start'] = $startDate;
        $this->filters[$type][$field]['end']   = $endDate;

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

        $this->enabledFilters[$field]['id']    = $field;
        $this->enabledFilters[$field]['label'] = $label;

        if (count($values) === 0) {
            $this->clearFilter($field, emit: false);
        }

        $this->afterChangedMultiSelectFilter($field, $values);

        $this->persistState('filters');
    }

    public function filterSelect(string $field, string $label): void
    {
        $this->resetPage();

        $this->enabledFilters[$field]['id']    = $field;
        $this->enabledFilters[$field]['label'] = $label;

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

        store($this)->set('filters.number.' . $field . '.thousands', $thousands);
        store($this)->set('filters.number.' . $field . '.decimal', $decimal);

        $this->enabledFilters[$field]['id']    = $field;
        $this->enabledFilters[$field]['label'] = $title;

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

        store($this)->set('filters.number.' . $field . '.thousands', $thousands);
        store($this)->set('filters.number.' . $field . '.decimal', $decimal);

        $this->enabledFilters[$field]['id']    = $field;
        $this->enabledFilters[$field]['label'] = $title;

        if (blank($value)) {
            $this->clearFilter($field, emit: false);
        }

        $this->afterChangedNumberEndFilter($field, $title, $value);

        $this->persistState('filters');
    }

    public function filterBoolean(string $field, string $value, string $label): void
    {
        $this->resetPage();

        $this->enabledFilters[$field]['id']    = $field;
        $this->enabledFilters[$field]['label'] = $label;

        if ($value == 'all') {
            $this->clearFilter($field, emit: false);
        }

        $this->afterChangedBooleanFilter($field, $label, $value);

        $this->persistState('filters');
    }

    public function filterInputText(string $field, string $value, string $label = ''): void
    {
        $this->resetPage();

        $this->enabledFilters[$field]['id']    = $field;
        $this->enabledFilters[$field]['label'] = $label;

        if (blank($value)) {
            $this->clearFilter($field, emit: false);
        }

        $this->afterChangedInputTextFilter($field, $label, $value);

        $this->persistState('filters');
    }

    public function filterInputTextOptions(string $field, string $value): void
    {
        data_set($this->filters, 'input_text_options.' . $field, $value);

        $this->enabledFilters[$field]['id'] = $field;

        $this->resetPage();

        $this->enabledFilters[$field]['disabled'] = false;

        if (in_array($value, ['is_empty', 'is_not_empty', 'is_null', 'is_not_null', 'is_blank', 'is_not_blank'])) {
            $this->enabledFilters[$field]['disabled'] = true;

            if (str($field)->contains('.')) {
                $this->filters['input_text'][str($field)->before('.')->toString()][str($field)->after('.')->toString()] = null;
            } else {
                $this->filters['input_text'][$field] = null;
            }
        }

        if (blank($value)) {
            $this->clearFilter($field, emit: false);
        }

        $this->persistState('filters');
    }

    private function clearFilterByTableAndColumn(string $table, ?string $column): void
    {
        $keys = [$table, $table . '.' . $column];

        foreach ($keys as $key) {
            $this->clearFilterByKey($key, $table, $column);
        }
    }

    private function clearFilterByKey(string $key, string $table, ?string $column): void
    {
        if (isset($this->filters['multi_select'][$key])) {
            $this->dispatch('pg:clear_multi_select::' . $this->tableName . ':' . $key);
        }

        if (isset($this->filters['datetime'][$key]) || isset($this->filters['date'][$key])) {
            $this->dispatch('pg:clear_flatpickr::' . $this->tableName . ':' . $key);
        }

        $filterTypes = ['input_text', 'input_text_options', 'number', 'boolean', 'datetime', 'date', 'select', 'multi_select'];

        foreach ($filterTypes as $type) {
            $this->unsetFilter($type, $key, $table, $column);
        }
    }

    private function unsetFilter(string $type, string $key, string $table, ?string $column): void
    {
        unset($this->filters[$type][$key]);
        unset($this->filters[$type][$table][$column]);

        if (empty($this->filters[$type][$table])) {
            unset($this->filters[$type][$table]);
        }
    }

    private function resolveFilters(): void
    {
        $filters = collect($this->filters());

        if ($filters->isEmpty()) {
            return;
        }

        /** @var Column $column */
        foreach ($this->columns as $column) {
            if (str(strval(data_get($column, 'dataField')))->contains('.')) {
                $field = strval(data_get($column, 'field'));
            } else {
                $field = filled(data_get($column, 'dataField')) ? data_get($column, 'dataField') : data_get($column, 'field');
            }

            $filterForColumn = $filters->filter(
                fn ($filter) => data_get($filter, 'column') == $field
            );

            if ($filterForColumn->count() > 0) {
                $filterForColumn->transform(function ($filter) use ($column) {
                    data_set($filter, 'title', data_get($column, 'title'));

                    return $filter;
                });

                data_set($column, 'filters', $filterForColumn->map(function ($filter) {
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
                        return (array) $filter->execute();
                    }

                    return (array) $filter;
                }));

                $filterForColumn->each(function ($filter) {
                    if (data_get($filter, 'className') === 'PowerComponents\LivewirePowerGrid\Components\Filters\FilterDynamic' &&
                        filled(data_get($filter, 'attributes'))) {
                        $attributes = array_values((array) data_get($filter, 'attributes'));

                        foreach ($attributes as $value) {
                            if (is_string($value) && str_contains($value, 'filters.')) {
                                data_set($this->filters, str($value)->replace('filters.', ''), null);
                            }
                        }
                    }
                });

                continue;
            }

            data_set($column, 'filters', collect([]));
        }
    }
}
