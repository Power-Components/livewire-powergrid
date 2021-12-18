<?php

namespace PowerComponents\LivewirePowerGrid\Traits;

use Illuminate\Support\{Arr, Collection};
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
        $this->search = '';

        unset($this->enabledFilters[$field]);
        unset($this->filters['number'][$field]);
        unset($this->filters['input_text'][$field]);
        unset($this->filters['boolean'][$field]);
        unset($this->filters['input_text_options'][$field]);

        $this->filters = [];
    }

    private function renderFilter(): void
    {
        $this->filters = [];
        $makeFilters   = [];

        /** @var Column $column */
        foreach ($this->columns as $column) {
            if (!isset($column->inputs)) {
                continue;
            }
            foreach ($column->inputs as $key => $input) {
                data_set($input, 'dataField', ($column->dataField != '') ? $column->dataField : $column->field);
                data_set($input, 'field', $column->field);
                data_set($input, 'label', $column->title);
                $makeFilters[$key][]  = $input;
            }
        }
        $this->makeFilters = collect($makeFilters);

        $path = 'livewire-powergrid::datatable.input_text_options';

        $this->inputTextOptions = [
            'contains'     => "$path.contains",
            'contains_not' => "$path.contains_not",
            'is'           => "$path.is",
            'is_not'       => "$path.is_not",
            'starts_with'  => "$path.starts_with",
            'ends_with'    => "$path.ends_with",
            'is_null'      => "$path.is_null",
            'is_not_null'  => "$path.is_not_null",
            'is_blank'     => "$path.is_blank",
            'is_not_blank' => "$path.is_not_blank",
            'is_empty'     => "$path.is_empty",
            'is_not_empty' => "$path.is_not_empty",
        ];
    }

    public function eventChangeDatePiker(array $data): void
    {
        $input                                   = explode('.', $data['values']);

        $this->enabledFilters[$data['field']]['data-field']      = $data['field'];
        $this->enabledFilters[$data['field']]['label']           = $data['label'];

        if (count($input) === 3) {
            $this->filters['date_picker'][$input[2]] = $data['selectedDates'];

            return;
        }

        if (count($input) === 4) {
            $this->filters['date_picker'][$input[2] . '.' . $input[3]] = $data['selectedDates'];

            return;
        }

        if (count($input) === 5) {
            $this->filters['date_picker'][$input[2] . '.' . $input[3] . '.' . $input[4]] = $data['selectedDates'];
        }
    }

    public function eventMultiSelect(array $data): void
    {
        $this->filters['multi_select'][$data['id']] = $data;

        $filter = collect($this->makeFilters->get('multi_select'))->where('data_field', $data['id']);

        $this->enabledFilters[$data['id']]['id']            = $data['id'];
        $this->enabledFilters[$data['id']]['label']         = $filter->first()['label'];

        $this->resetPage();

        if (count($data['values']) === 0) {
            $this->clearFilter($data['id']);
        }
    }

    public function filterSelect(string $field, string $label): void
    {
        $this->enabledFilters[$field]['id']         = $field;
        $this->enabledFilters[$field]['label']      = $label;

        $this->resetPage();

        if (data_get($this->filters, "select.$field") === '') {
            $this->clearFilter($field);
        }
    }

    public function filterNumberStart(string $field, string $value, string $thousands, string $decimal, string $label): void
    {
        $this->filters['number'][$field]['start']     = $value;
        $this->filters['number'][$field]['thousands'] = $thousands;
        $this->filters['number'][$field]['decimal']   = $decimal;

        $this->enabledFilters[$field]['id']          = $field;
        $this->enabledFilters[$field]['label']       = $label;

        $this->resetPage();

        if ($value == '') {
            $this->clearFilter($field);
        }
    }

    public function filterNumberEnd(string $field, string $value, string $thousands, string $decimal, string $label): void
    {
        $this->filters['number'][$field]['end']       = $value;
        $this->filters['number'][$field]['thousands'] = $thousands;
        $this->filters['number'][$field]['decimal']   = $decimal;

        $this->enabledFilters[$field]['id']          = $field;
        $this->enabledFilters[$field]['label']       = $label;

        $this->resetPage();

        if ($value == '') {
            $this->clearFilter($field);
        }
    }

    public function filterInputText(string $field, string $value, string $label): void
    {
        $this->enabledFilters[$field]['id']          = $field;
        $this->enabledFilters[$field]['label']       = $label;

        $this->resetPage();

        if ($value == '') {
            $this->clearFilter($field);
        }
    }

    public function filterBoolean(string $field, string $value, string $label): void
    {
        $this->filters['boolean'][$field] = $value;

        $this->enabledFilters[$field]['id']          = $field;
        $this->enabledFilters[$field]['label']       = $label;

        $this->resetPage();

        if ($value == 'all') {
            $this->clearFilter($field);
        }
    }

    public function filterInputTextOptions(string $field, string $value, string $label): void
    {
        $this->filters['input_text_options'][$field] = $value;

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
    }
}
