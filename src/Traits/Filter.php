<?php

namespace PowerComponents\LivewirePowerGrid\Traits;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;

trait Filter
{
    public Collection $make_filters;
    public array $filters = [];
    public array $filters_enabled = [];
    public array $select = [];
    private string $format_date = '';

    public function clearFilter($field = '')
    {
        $this->search = '';
        unset($this->filters_enabled[$field]);
        $this->filters = [];
    }

    private function renderFilter()
    {
        $this->filters = [];
        $make_filters = [];

        foreach ($this->columns as $column) {
            if (isset($column->inputs)) {
                foreach ($column->inputs as $key => $input) {
                    $input['data_field'] = ($column->data_field != '') ? $column->data_field : $column->field;
                    $input['field'] = $column->field;
                    $input['label'] = $column->title;
                    $make_filters[$key][] = $input;
                }
            }
        }
        $this->make_filters = collect($make_filters);

    }

    /**
     * @param array $data
     */
    public function eventChangeDatePiker(array $data): void
    {
        $input = explode('.', $data['values']);
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
        $this->filters['number'][$field]['start'] = $value;
        $this->filters['number'][$field]['thousands'] = $thousands;
        $this->filters['number'][$field]['decimal'] = $decimal;
    }

    /**
     * @param string $field
     * @param string $value
     * @param string $thousands
     * @param string $decimal
     */
    public function filterNumberEnd(string $field, string $value, string $thousands, string $decimal): void
    {
        $this->filters['number'][$field]['end'] = $value;
        $this->filters['number'][$field]['thousands'] = $thousands;
        $this->filters['number'][$field]['decimal'] = $decimal;
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

    /**
     * Validate if the given value is valid as an Input Option
     *
     * @param string $field Field to be checked
     * @return bool
     */
    private function validateInputTextOptions(string $field)
    {

        return isset($this->filters['input_text_options'][$field]) && in_array(strtolower($this->filters['input_text_options'][$field]),
                ['is', 'is_not', 'contains', 'contains_not', 'starts_with', 'ends_with']);

    }

    /**
     * @param string $field
     * @param $value
     */
    private function usingDatePicker($collection, string $field, $value)
    {
        if (isset($value[0]) && isset($value[1])) {
            $collection->whereBetween($field, [Carbon::parse($value[0]), Carbon::parse($value[1])]);
        }

    }

    /**
     * @param string $field
     * @param $value
     */
    private function usingInputText($query, string $field, $value)
    {

        $textFieldOperator = ($this->validateInputTextOptions($field) ? strtolower($this->filters['input_text_options'][$field]) : 'contains');

        if ($textFieldOperator == 'is') {
            $query->where($field, '=', $value);
        }

        if ($textFieldOperator == 'is_not') {
            $query->where($field, '!=', $value);
        }

        if ($textFieldOperator == 'starts_with') {
            $query->where($field, 'like', $value.'%');
        }

        if ($textFieldOperator == 'ends_with') {
            $query->where($field, 'like', '%'.$value);
        }

        if ($textFieldOperator == 'contains') {
            $query->where($field, 'like', '%'.$value.'%');
        }

        if ($textFieldOperator == 'contains_not') {
            $query->where($field, 'not like', '%'.$value.'%');
        }
    }

    /**
     * @param string $field
     * @param $value
     */
    private function usingBoolean($collection, string $field, $value)
    {
        if ($value != "all") {
            $value = ($value == "true");
            $collection->where($field, '=', $value);
        }
    }

    /**
     * @param string $field
     * @param $value
     */
    private function usingSelect($collection, string $field, $value)
    {
        $collection->where($field, $value);
    }

    /**
     * @param string $field
     * @param $value
     */
    private function usingMultiSelect($collection, string $field, $value)
    {
        if (count(collect($value)->get('values'))) {
            $collection->whereIn($field, collect($value)->get('values'));
        }
    }

    /**
     * @param string $field
     * @param $value
     */
    private function usingNumber($collection, string $field, $value)
    {
        if (isset($value['start']) && !isset($value['end'])) {
            $start = str_replace($value['thousands'], '', $value['start']);
            $start = (float)str_replace($value['decimal'], '.', $start);

            $collection->where($field, '>=', $start);
        }
        if (!isset($value['start']) && isset($value['end'])) {
            $end = str_replace($value['thousands'], '', $value['end']);
            $end = (float)str_replace($value['decimal'], '.', $end);

            $collection->where($field, '<=', $end);
        }
        if (isset($value['start']) && isset($value['end'])) {
            $start = str_replace($value['thousands'], '', $value['start']);
            $start = str_replace($value['decimal'], '.', $start);

            $end = str_replace($value['thousands'], '', $value['end']);
            $end = str_replace($value['decimal'], '.', $end);

            $collection->whereBetween($field, [$start, $end]);
        }
    }
}
