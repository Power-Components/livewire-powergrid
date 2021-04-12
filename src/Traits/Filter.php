<?php

namespace PowerComponents\LivewirePowerGrid\Traits;

use Carbon\Carbon;
use Illuminate\Support\Collection;

trait Filter
{
    public Collection $make_filters;

    public array $filters = [];

    public bool $filter_action = false;

    private string $format_date = '';

    public function filter()
    {
        $this->filter_action = true;
    }

    public function clearFilter()
    {
        $this->filter_action = false;
        $this->search = '';
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
        foreach ($this->filters as $type => $filter) {
            switch ($type) {
                case 'date_picker':
                    $date = $filter[key($filter)];
                    if (isset($date[0]) && isset($date[1])) {
                        $collection = $collection->whereBetween(key($filter), [Carbon::parse($date[0]), Carbon::parse($date[1])]);
                    }
                    break;
                case 'select':
                    if (filled($filter[key($filter)])) {
                        $key = key($filter);
                        $value = $filter[$key];
                        $collection = $collection->where($key, $value);
                    }
                    break;
            }
        }

        return $collection;
    }

    public function inputDatePiker( $data )
    {
        $input = explode('.', $data[0]['values']);
        $this->filters['date_picker'][$input[2]] = $data[0]['selectedDates'];
        $this->filter_action = true;
    }


}
