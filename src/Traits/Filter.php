<?php

namespace PowerComponents\LivewirePowerGrid\Traits;

use Carbon\Carbon;
use Illuminate\Support\Collection;

trait Filter
{
    public Collection $make_filters;
    public array $filters = [];
    private string $format_date = '';

    public function clearFilter()
    {
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
            $key = key($filter);

            if (filled($filter[key($filter)])) {
                $value = $filter[$key];
                switch ($type) {
                    case 'date_picker':
                        if (isset($value[0]) && isset($value[1])) {
                            $collection = $collection->whereBetween($key, [Carbon::parse($value[0]), Carbon::parse($value[1])]);
                        }
                        break;
                    case 'select':
                        $collection = $collection->where($key, $value);
                        break;
                    case 'number':
                        if (isset($value['start']) && isset($value['end'])) {
                            $collection = $collection->whereBetween($key, [$value['start'], $value['end']]);
                        }
                        break;
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

    public function filterNumberStart( $field, $value ): void
    {
        $this->filters['number'][$field]['start'] = $value;
    }

    public function filterNumberEnd( $field, $value ): void
    {
        $this->filters['number'][$field]['end'] = $value;
    }

}
