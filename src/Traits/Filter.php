<?php

namespace PowerComponents\LivewirePowerGrid\Traits;

use Carbon\Carbon;
use Illuminate\Support\Collection;

trait Filter
{
    public Collection $make_filters;
    public array $filters = [];
    public array $filters_enabled = [];
    private string $format_date = '';
    public array $select = [];

    public function clearFilter( $field = '' )
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

    private function advancedFilter( Collection $collection ): Collection
    {
        foreach ($this->filters as $key => $type) {
            foreach ($type as $field => $value) {

                if (filled($value)) {
                    switch ($key) {
                        case 'date_picker':
                            if (isset($value[0]) && isset($value[1])) {
                                $collection = $collection->whereBetween($field, [Carbon::parse($value[0]), Carbon::parse($value[1])]);
                            }
                            break;
                        case 'multi_select':
                            if (count(collect($value)->get('values'))) {
                                $collection = $collection->whereIn($field, collect($value)->get('values'));
                            }
                            break;
                        case 'select':
                            $collection = $collection->where($field, $value);
                            break;
                        case 'input_text':
                            $collection = $collection->filter(function ($row) use ($field, $value) {
                                return false !== stristr($row->$field, strtolower($value));
                            });
                            break;
                        case 'number':

                            if (isset($value['start']) && !isset($value['end'])) {
                                $start = str_replace($value['thousands'], '', $value['start']);
                                $start = (float)str_replace($value['decimal'], '.', $start);

                                $collection = $collection->where($field, '>=', $start);
                            }
                            if (!isset($value['start']) && isset($value['end'])) {
                                $end = str_replace($value['thousands'], '', $value['end']);
                                $end = (float)str_replace($value['decimal'], '.', $end);

                                $collection = $collection->where($field, '<=', $end);
                            }
                            if (isset($value['start']) && isset($value['end'])) {
                                $start = str_replace($value['thousands'], '', $value['start']);
                                $start = str_replace($value['decimal'], '.', $start);

                                $end = str_replace($value['thousands'], '', $value['end']);
                                $end = str_replace($value['decimal'], '.', $end);

                                $collection = $collection->whereBetween($field, [$start, $end]);
                            }
                            break;
                    }
                }
            }
        }
        return $collection;
    }

    /**
     * @param $data
     */
    public function eventChangeDatePiker(array $data ): void
    {
        $input = explode('.', $data['values']);
        $this->filters['date_picker'][$input[2]] = $data['selectedDates'];
    }

    /**
     * @param $data
     */
    public function eventMultiSelect(array $data )
    {
        $this->filters['multi_select'][$data['id']] = $data;
    }

    /**
     * @param string $field
     * @param string $value
     * @param string $thousands
     * @param string $decimal
     */
    public function filterNumberStart( string $field, string $value, string $thousands, string $decimal ): void
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
    public function filterNumberEnd( string $field, string $value, string $thousands, string $decimal ): void
    {
        $this->filters['number'][$field]['end'] = $value;
        $this->filters['number'][$field]['thousands'] = $thousands;
        $this->filters['number'][$field]['decimal'] = $decimal;
    }

    /**
     * @param string $field
     * @param string $value
     */
    public function filterInputText( string $field, string $value ): void
    {
        $this->filters['input_text'][$field] = $value;
    }


}
