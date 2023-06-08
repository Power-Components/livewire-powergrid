<?php

namespace PowerComponents\LivewirePowerGrid\Traits;

use DateTimeZone;
use Illuminate\Support\{Arr, Carbon, Collection};
use PowerComponents\LivewirePowerGrid\Column;

trait HasFilter
{
    public array $filters = [];

    public array $enabledFilters = [];

    public array $select = [];

    public function clearFilter(string $field = '', bool $emit = true): void
    {
        if (str_contains($field, '.')) {
            list($table, $column) = explode('.', $field);

            if (isset($this->filters['multi_select'][$table][$column])) {
                $this->dispatchBrowserEvent('pg:clear_multi_select::' . $this->tableName);
            }

            if (isset($this->filters['datetime'][$table][$column]) || isset($this->filters['date'][$table][$column])) {
                $this->dispatchBrowserEvent('pg:clear_flatpickr::' . $this->tableName . ':' . $field);
            }

            unset($this->filters['input_text'][$table][$column]);
            unset($this->filters['input_text_options'][$table][$column]);
            unset($this->filters['number'][$table][$column]['start']);
            unset($this->filters['number'][$table][$column]['end']);
            unset($this->filters['boolean'][$table][$column]);
            unset($this->filters['datetime'][$table][$column]);
            unset($this->filters['date'][$table][$column]);
            unset($this->filters['select'][$table][$column]);
            unset($this->filters['multi_select'][$table][$column]);

            unset($this->filters['input_text'][$table . '.' . $column]);
            unset($this->filters['input_text_options'][$table . '.' . $column]);
            unset($this->filters['number'][$table . '.' . $column]['start']);
            unset($this->filters['number'][$table . '.' . $column]['end']);
            unset($this->filters['boolean'][$table . '.' . $column]);
            unset($this->filters['datetime'][$table . '.' . $column]);
            unset($this->filters['date'][$table . '.' . $column]);
            unset($this->filters['select'][$table . '.' . $column]);
            unset($this->filters['multi_select'][$table . '.' . $column]);

            if (empty($this->filters['input_text'][$table])) {
                unset($this->filters['input_text'][$table]);
            }

            if (empty($this->filters['input_text_options'][$table])) {
                unset($this->filters['input_text_options'][$table]);
            }

            if (empty($this->filters['number'][$table]['start'])) {
                unset($this->filters['number'][$table]['start']);
            }

            if (empty($this->filters['number'][$table]['end'])) {
                unset($this->filters['number'][$table]['end']);
            }

            if (empty($this->filters['boolean'][$table])) {
                unset($this->filters['boolean'][$table]);
            }

            if (empty($this->filters['datetime'][$table])) {
                unset($this->filters['datetime'][$table]);
            }

            if (empty($this->filters['date'][$table])) {
                unset($this->filters['date'][$table]);
            }

            if (empty($this->filters['select'][$table])) {
                unset($this->filters['select'][$table]);
            }

            if (empty($this->filters['multi_select'][$table])) {
                unset($this->filters['multi_select'][$table]);
            }
        } else {
            if (isset($this->filters['multi_select'][$field])) {
                $this->dispatchBrowserEvent('pg:clear_multi_select::' . $this->tableName);
            }

            if (isset($this->filters['datetime'][$field]) || isset($this->filters['date'][$field])) {
                $this->dispatchBrowserEvent('pg:clear_flatpickr::' . $this->tableName . ':' . $field);
            }

            unset($this->filters['input_text'][$field]);
            unset($this->filters['input_text_options'][$field]);
            unset($this->filters['number'][$field]['start']);
            unset($this->filters['number'][$field]['end']);
            unset($this->filters['boolean'][$field]);
            unset($this->filters['datetime'][$field]);
            unset($this->filters['date'][$field]);
            unset($this->filters['select'][$field]);
            unset($this->filters['multi_select'][$field]);
        }

        unset($this->enabledFilters[$field]);

        if ($emit) {
            $this->emit('pg:events', ['event' => 'clearFilters', 'field' => $field, 'tableName' => $this->tableName]);
        }

        $this->persistState('filters');
    }

    public function clearAllFilters(): void
    {
        $this->enabledFilters = [];
        $this->filters        = [];

        $this->persistState('filters');

        $this->dispatchBrowserEvent('pg:clear_all_flatpickr::' . $this->tableName);
    }

    private function resolveFilters(): void
    {
        $filters = collect($this->filters());

        /** @var Column $column */
        foreach ($this->columns as $column) {
            if (str($column->dataField)->contains('.')) {
                $field = $column->field;
            } else {
                $field = filled($column->dataField) ? $column->dataField : $column->field;
            }

            $filterForColumn = $filters->filter(
                fn ($filter) => $filter->column == $field
            );

            if ($filterForColumn->count() > 0) {
                $filterForColumn->transform(function ($filter) use ($column) {
                    $filter->title = $column->title;

                    return $filter;
                });

                data_set($column, 'filters', $filterForColumn->map(function ($filter) {
                    if (!app()->runningUnitTests()) {
                        unset($filter->builder, $filter->collection);
                    }

                    if (method_exists($filter, 'execute')) {
                        return (array) $filter->execute();
                    }

                    return (array) $filter;
                }));

                $filterForColumn->each(function ($filter) {
                    if ($filter->className === 'PowerComponents\LivewirePowerGrid\Filters\FilterDynamic' &&
                        isset($filter->attributes)) {
                        $attributes = array_values($filter->attributes);

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

    public function datePickerChanged(array $data): void
    {
        $this->resetPage();

        $input = explode('.', $data['values']);

        $startDate = strval(data_get($data, 'selectedDates.0'));
        $endDate   = strval(data_get($data, 'selectedDates.1'));

        $appTimeZone = strval(config('app.timezone'));

        $filterTimezone = new DateTimeZone($data['timezone'] ?? 'UTC');

        $startDate = Carbon::parse($startDate)->format('Y-m-d');
        $endDate   = Carbon::parse($endDate)->format('Y-m-d');

        $startDate = Carbon::createFromFormat('Y-m-d', $startDate, $filterTimezone);
        $endDate   = Carbon::createFromFormat('Y-m-d', $endDate, $filterTimezone);

        if ($data['type'] === 'datetime') {
            $startDate->startOfDay()->setTimeZone($appTimeZone);
            $endDate->endOfDay()->setTimeZone($appTimeZone);
        }

        data_set($data, 'selectedDates.0', $startDate);
        data_set($data, 'selectedDates.1', $endDate);

        $this->enabledFilters[$data['field']]['data-field'] = $data['field'];
        $this->enabledFilters[$data['field']]['label']      = $data['label'];

        if (count($input) === 3) {
            $this->filters[$data['type']][$input[2]] = $data['selectedDates'];
            $this->persistState('filters');

            return;
        }

        if (count($input) === 4) {
            $this->filters[$data['type']][$input[2] . '.' . $input[3]] = $data['selectedDates'];
            $this->persistState('filters');

            return;
        }

        if (count($input) === 5) {
            $this->filters[$data['type']][$input[2] . '.' . $input[3] . '.' . $input[4]] = $data['selectedDates'];
            $this->persistState('filters');
        }
    }

    public function multiSelectChanged(array $params): void
    {
        $this->resetPage();

        $field  = strval(data_get($params, 'params.dataField'));
        $values = (array) data_get($params, 'values');

        data_set($this->filters, "multi_select.$field", $values);

        $this->enabledFilters[$field]['id']    = $field;
        $this->enabledFilters[$field]['label'] = strval(data_get($params, 'params.title'));

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

    public function filterNumberStart(array $params, string $value): void
    {
        extract($params);

        $this->resetPage();

        $this->filters['number'][$field]['start']     = $value;
        $this->filters['number'][$field]['thousands'] = $thousands;
        $this->filters['number'][$field]['decimal']   = $decimal;

        $this->enabledFilters[$field]['id']    = $field;
        $this->enabledFilters[$field]['label'] = $title;

        if (blank($value)) {
            $this->clearFilter($field, emit: false);
        }

        $this->afterChangedNumberStartFilter($field, $title, $value);

        $this->persistState('filters');
    }

    public function filterNumberEnd(array $params, string $value): void
    {
        extract($params);

        $this->resetPage();

        $this->filters['number'][$field]['end']       = $value;
        $this->filters['number'][$field]['thousands'] = $thousands;
        $this->filters['number'][$field]['decimal']   = $decimal;

        $this->enabledFilters[$field]['id']    = $field;
        $this->enabledFilters[$field]['label'] = $title;

        if (blank($value)) {
            $this->clearFilter($field, emit: false);
        }

        $this->afterChangedNumberEndFilter($field, $value, $title);

        $this->persistState('filters');
    }

    public function filterBoolean(string $field, string $value, string $label): void
    {
        $this->resetPage();

        $this->filters['boolean'][$field] = $value;

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
            $this->filters['input_text'][$field]      = null;
        }

        if (blank($value)) {
            $this->clearFilter($field, emit: false);
        }

        $this->persistState('filters');
    }
}
