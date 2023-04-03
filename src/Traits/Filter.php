<?php

namespace PowerComponents\LivewirePowerGrid\Traits;

use DateTimeZone;
use Illuminate\Support\{Arr, Carbon, Collection};
use PowerComponents\LivewirePowerGrid\Column;

trait Filter
{
    public Collection $makeFilters;

    public array $filters = [];

    public array $enabledFilters = [];

    public array $select = [];

    public array $inputTextOptions = [];

    public function clearFilter(string $field = ''): void
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
            if (empty($this->filters['number_start'][$table])) {
                unset($this->filters['number_start'][$table]);
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

        $this->persistState('filters');
    }

    public function clearAllFilters(): void
    {
        $this->enabledFilters = [];
        $this->filters        = [];

        $this->persistState('filters');

        $this->dispatchBrowserEvent('pg:clear_all_flatpickr::' . $this->tableName);
    }

    public static function getInputTextOptions(): array
    {
        return [
            'contains',
            'contains_not',
            'is',
            'is_not',
            'starts_with',
            'ends_with',
            'is_empty',
            'is_not_empty',
            'is_null',
            'is_not_null',
            'is_blank',
            'is_not_blank',
        ];
    }

    private function resolveFilters(): void
    {
        $makeFilters   = [];

        /** @var Column $column */
        foreach ($this->columns as $column) {
            if (!isset($column->inputs)) {
                continue;
            }
            foreach ($column->inputs as $key => $input) {
                if (!isset($input['dataField'])) {
                    data_set($input, 'dataField', $column->dataField ?: $column->field);
                }
                data_set($input, 'field', $column->field);
                data_set($input, 'label', $column->title);
                $makeFilters[$key][]  = $input;
            }
        }

        $this->makeFilters = collect($makeFilters);

        $path = 'livewire-powergrid::datatable.input_text_options';

        foreach (self::getInputTextOptions() as $option) {
            $this->inputTextOptions[$option] = "$path.$option";
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
            $startDate->setTime(0, 0, 0)->setTimeZone($appTimeZone);
            $endDate->setTime(23, 59, 59)->setTimeZone($appTimeZone);
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

    public function multiSelectChanged(array $data): void
    {
        $this->resetPage();

        $field      = $data['id'];
        $values     = $data['values'];

        unset($data['id']);
        unset($data['values']);

        $this->filters['multi_select'][$field] = $values;

        /** @var array $multiSelect */
        $multiSelect = $this->makeFilters->get('multi_select');

        /** @var array $filter */
        $filter = collect($multiSelect)->where('dataField', $field)->first();

        $this->enabledFilters[$field]['id']            = $field;
        $this->enabledFilters[$field]['label']         = $filter['label'];

        if (count($values) === 0) {
            $this->clearFilter($field);
        }

        $this->persistState('filters');

        $this->onUpdatedMultiSelect($field, $values);
    }

    public function filterSelect(string $field, string $label): void
    {
        $this->resetPage();

        $this->enabledFilters[$field]['id']         = $field;
        $this->enabledFilters[$field]['label']      = $label;

        if (data_get($this->filters, "select.$field") === '') {
            $this->clearFilter($field);
        }

        $this->persistState('filters');
    }

    public function filterNumberStart(string $field, string $value, string $label): void
    {
        $this->resetPage();

        $this->filters['number'][$field]['start']     = $value;

        $this->enabledFilters[$field]['id']          = $field;
        $this->enabledFilters[$field]['label']       = $label;

        if ($value == '') {
            $this->clearFilter($field);
        }

        $this->persistState('filters');
    }

    public function filterNumberEnd(string $field, string $value, string $label): void
    {
        $this->resetPage();

        $this->filters['number'][$field]['end']       = $value;

        $this->enabledFilters[$field]['id']          = $field;
        $this->enabledFilters[$field]['label']       = $label;

        if ($value == '') {
            $this->clearFilter($field);
        }

        $this->persistState('filters');
    }

    public function filterInputText(string $field, string $value, string $label): void
    {
        $this->resetPage();

        $this->enabledFilters[$field]['id']          = $field;
        $this->enabledFilters[$field]['label']       = $label;

        if ($value == '') {
            $this->clearFilter($field);
        }

        $this->persistState('filters');
    }

    public function filterBoolean(string $field, string $value, string $label): void
    {
        $this->resetPage();

        $this->filters['boolean'][$field] = $value;

        $this->enabledFilters[$field]['id']          = $field;
        $this->enabledFilters[$field]['label']       = $label;

        if ($value == 'all') {
            $this->clearFilter($field);
        }

        $this->persistState('filters');
    }

    public function filterInputTextOptions(string $field, string $value, string $label): void
    {
        data_set($this->filters, 'input_text_options.' . $field, $value);
        // $this->filters['input_text_options'][$field] = $value;

        $this->enabledFilters[$field]['id']          = $field;
        $this->enabledFilters[$field]['label']       = $label;

        $this->resetPage();

        if (in_array($value, ['is_empty', 'is_not_empty', 'is_null', 'is_not_null', 'is_blank', 'is_not_blank'])) {
            $this->enabledFilters[$field]['disabled']       = true;
            $this->filters['input_text'][$field]            = null;
        } else {
            $this->enabledFilters[$field]['disabled']       = false;
        }

        if ($value == '') {
            $this->clearFilter($field);
        }

        $this->persistState('filters');
    }
}
