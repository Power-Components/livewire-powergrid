<?php

namespace PowerComponents\LivewirePowerGrid\Traits;

use Illuminate\Support\Collection;

trait Filter
{
    public Collection $makeFilters;

    public array $filters = [];

    public array $enabledFilters = [];

    public array $select = [];

    public function clearFilter(string $field = '')
    {
        $this->search = '';

        unset($this->enabledFilters[$field]);
        unset($this->filters['number'][$field]);
        unset($this->filters['input_text'][$field]);
        unset($this->filters['boolean'][$field]);
        unset($this->filters['input_text_options'][$field]);

        $this->filters = [];
    }

    private function renderFilter()
    {
        $this->filters = [];
        $makeFilters   = [];

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
    }

    /**
     * @param array $data
     */
    public function eventChangeDatePiker(array $data): void
    {
        $input                                   = explode('.', $data['values']);
        $this->filters['date_picker'][$input[2]] = $data['selectedDates'];

        $this->enabledFilters[$data['field']]['data-field']      = $data['field'];
        $this->enabledFilters[$data['field']]['label']           = $data['label'];
    }

    /**
     * @param array $data
     */
    public function eventMultiSelect(array $data)
    {
        $this->filters['multi_select'][$data['id']] = $data;

        $filter = collect($this->makeFilters->get('multi_select'))->where('data_field', $data['id']);

        $this->enabledFilters[$data['id']]['id']            = $data['id'];
        $this->enabledFilters[$data['id']]['label']         = $filter->first()['label'];

        if (count($data['values']) === 0) {
            $this->clearFilter($data['id']);
        }
    }

    public function filterSelect(string $field, string $label)
    {
        $this->enabledFilters[$field]['id']         = $field;
        $this->enabledFilters[$field]['label']      = $label;

        if (data_get($this->filters, "select.$field") === '') {
            $this->clearFilter($field);
        }
    }

    /**
     * @param string $field
     * @param string $value
     * @param string $thousands
     * @param string $decimal
     * @param string $label
     */
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

    /**
     * @param string $field
     * @param string $value
     * @param string $thousands
     * @param string $decimal
     * @param string $label
     */
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

    /**
     * @param string $field
     * @param string $value
     * @param string $label
     */
    public function filterInputText(string $field, string $value, string $label): void
    {
        $this->filters['input_text'][$field] = $value;

        $this->enabledFilters[$field]['id']          = $field;
        $this->enabledFilters[$field]['label']       = $label;

        $this->resetPage();

        if ($value == '') {
            $this->clearFilter($field);
        }
    }

    /**
     * @param string $field
     * @param string $value
     * @param string $label
     */
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

    /**
     * @param string $field
     * @param string $value
     * @param string $label
     */
    public function filterInputTextOptions(string $field, string $value, string $label): void
    {
        $this->filters['input_text_options'][$field] = $value;

        $this->enabledFilters[$field]['id']          = $field;
        $this->enabledFilters[$field]['label']       = $label;

        $this->resetPage();

        if ($value == '') {
            $this->clearFilter($field);
        }
    }
}
