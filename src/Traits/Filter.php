<?php

namespace PowerComponents\LivewirePowerGrid\Traits;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

trait Filter
{
    public Collection $make_filters;
    public array $filters = [];
    public array $filters_enabled = [];
    private string $format_date = '';

    public function clearFilter($field='')
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
                        case 'select':
                            $collection = $collection->where($field, $value);
                            break;
                        case 'number':
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

    public function inputDatePiker( $data ): void
    {
        $input = explode('.', $data[0]['values']);
        $this->filters['date_picker'][$input[2]] = $data[0]['selectedDates'];
    }

    public function filterNumberStart(string $field,string $value, string $column, string $thousands,string $decimal ): void
    {
        $this->filters['number'][$field]['start'] = $value;
        $this->filters['number'][$field]['thousands'] = $thousands;
        $this->filters['number'][$field]['decimal'] = $decimal;
    }

    public function filterNumberEnd(string $field,string $value, string $column, string $thousands,string $decimal ): void
    {
        $this->filters['number'][$field]['end'] = $value;
        $this->filters['number'][$field]['thousands'] = $thousands;
        $this->filters['number'][$field]['decimal'] = $decimal;
    }

}
