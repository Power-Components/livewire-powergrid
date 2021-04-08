<?php


namespace PowerComponents\LivewirePowerGrid\Traits;


use Carbon\Carbon;
use Illuminate\Support\Collection;

trait Filter
{
    /**
     * https://flatpickr.js.org
     */
    public array $defaultDatePikerConfig = [
        'mode' => 'range',
        'defaultHour' => 0,
        'locale' => 'pt',
        'dateFormat' => 'd/m/Y H:i',
        'enableTime' => true,
        'time_24hr' => true
    ];

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

    public function formatDate( string $format = '' ): string
    {
        if ($this->format_date === '') {
            return 'Y-m-d H:i:s';
        }
        if ($format === '') {
            return $this->format_date;
        }
        $this->format_date = $format;
        return $format;
    }

    private function advancedFilter( $data )
    {

        foreach ($this->filters as $type => $filter) {
            if ($type === 'date_picker') {
                $date = explode('até', $filter[key($filter)]);
                if (isset($date[1]) && filled($date[0]) && filled($date[1])) {

                    $from = Carbon::createFromFormat('d/m/Y H:i', trim($date[0]))->format($this->formatDate());
                    $to = Carbon::createFromFormat('d/m/Y H:i', trim($date[1]))->format($this->formatDate());

                    $key = key($filter);
                    $data = $data->whereBetween($key, [$from, $to]);

                }
            }
            if ($type === 'select') {
                if (filled($filter[key($filter)])) {
                    $key = key($filter);
                    $value = $filter[$key];
                    $data = $data->where($key, $value);
                }
            }
        }

        return $data;
    }

    public function inputDatePiker($data)
    {
        $input = explode('.', $data[0]['values']);
        $this->filters['date_picker'][$input[2]] = $data[0]['selectedDates'];
        $this->filter_action = true;
    }


}
