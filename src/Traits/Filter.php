<?php

namespace PowerComponents\LivewirePowerGrid\Traits;

use Illuminate\Support\Collection;

trait Filter
{
    public Collection $make_filters;

    public array $filters = [];

    public array $filters_enabled = [];

    public array $select = [];

    public function clearFilter($field = '')
    {
        $this->search = '';
        unset($this->filters_enabled[$field]);
        $this->filters = [];
    }

    private function renderFilter()
    {
        $this->filters = [];
        $make_filters  = [];

        foreach ($this->columns as $column) {
            if (!isset($column->inputs)) {
                continue;
            }
            foreach ($column->inputs as $key => $input) {
                $input['data_field']  = ($column->data_field != '') ? $column->data_field : $column->field;
                $input['field']       = $column->field;
                $input['label']       = $column->title;
                $make_filters[$key][] = $input;
            }
        }
        $this->make_filters = collect($make_filters);
    }

    /**
     * @param array $data
     */
    public function eventChangeDatePiker(array $data): void
    {
        $input                                   = explode('.', $data['values']);
        $this->filters['date_picker'][$input[2]] = $data['selectedDates'];
    }

    /**
     * @param $data
     */
    public function eventMultiSelect(array $data)
    {
        $this->filters['multi_select'][$data['id']] = $data;
    }

    /**
     * @param string $field
     * @param string $value
     * @param string $thousands
     * @param string $decimal
     */
    public function filterNumberStart(string $field, string $value, string $thousands, string $decimal): void
    {
        $this->filters['number'][$field]['start']     = $value;
        $this->filters['number'][$field]['thousands'] = $thousands;
        $this->filters['number'][$field]['decimal']   = $decimal;
    }

    /**
     * @param string $field
     * @param string $value
     * @param string $thousands
     * @param string $decimal
     */
    public function filterNumberEnd(string $field, string $value, string $thousands, string $decimal): void
    {
        $this->filters['number'][$field]['end']       = $value;
        $this->filters['number'][$field]['thousands'] = $thousands;
        $this->filters['number'][$field]['decimal']   = $decimal;
    }

    /**
     * @param string $field
     * @param string $value
     */
    public function filterInputText(string $field, string $value): void
    {
        $this->filters['input_text'][$field] = $value;
    }

    /**
     * @param string $field
     * @param string $value
     */
    public function filterBoolean(string $field, string $value): void
    {
        $this->filters['boolean'][$field] = $value;
    }

    /**
     * @param string $field
     * @param string $value
     */
    public function filterInputTextOptions(string $field, string $value): void
    {
        $this->filters['input_text_options'][$field] = $value;
    }
}
