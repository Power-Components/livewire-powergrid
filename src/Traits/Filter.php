<?php

namespace PowerComponents\LivewirePowerGrid\Traits;

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
        $this->search = '';

        unset($this->enabledFilters[$field]);
        unset($this->filters['number'][$field]);
        unset($this->filters['input_text'][$field]);
        unset($this->filters['boolean'][$field]);
        unset($this->filters['input_text_options'][$field]);

        $this->filters = [];
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

        foreach (self::getInputTextOptions() as $option) {
            $this->inputTextOptions[$option] = "$path.$option";
        }
    }

    public function datePikerChanged(array $data): void
    {
        $this->resetPage();

        $input                                   = explode('.', $data['values']);

        data_set($data, 'selectedDates.0', Carbon::parse(data_get($data, 'selectedDates.0'))->setTime(0, 0));
        data_set($data, 'selectedDates.1', Carbon::parse(data_get($data, 'selectedDates.1'))->setTime(23, 59, 59));

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

    public function multiSelectChanged(array $data): void
    {
        $this->resetPage();

        $this->filters['multi_select'][$data['id']] = $data;

        $filter = collect($this->makeFilters->get('multi_select'))->where('data_field', $data['id']);

        $this->enabledFilters[$data['id']]['id']            = $data['id'];
        $this->enabledFilters[$data['id']]['label']         = $filter->first()['label'];

        if (count($data['values']) === 0) {
            $this->clearFilter($data['id']);
        }
    }

    public function filterSelect(string $field, string $label): void
    {
        $this->resetPage();

        $this->enabledFilters[$field]['id']         = $field;
        $this->enabledFilters[$field]['label']      = $label;

        if (data_get($this->filters, "select.$field") === '') {
            $this->clearFilter($field);
        }
    }

    public function filterNumberStart(string $field, string $value, string $thousands, string $decimal, string $label): void
    {
        $this->resetPage();

        $this->filters['number'][$field]['start']     = $value;
        $this->filters['number'][$field]['thousands'] = $thousands;
        $this->filters['number'][$field]['decimal']   = $decimal;

        $this->enabledFilters[$field]['id']          = $field;
        $this->enabledFilters[$field]['label']       = $label;

        if ($value == '') {
            $this->clearFilter($field);
        }
    }

    public function filterNumberEnd(string $field, string $value, string $thousands, string $decimal, string $label): void
    {
        $this->resetPage();

        $this->filters['number'][$field]['end']       = $value;
        $this->filters['number'][$field]['thousands'] = $thousands;
        $this->filters['number'][$field]['decimal']   = $decimal;

        $this->enabledFilters[$field]['id']          = $field;
        $this->enabledFilters[$field]['label']       = $label;

        if ($value == '') {
            $this->clearFilter($field);
        }
    }

    public function filterInputText(string $field, string $value, string $label): void
    {
        $this->resetPage();

        $this->enabledFilters[$field]['id']          = $field;
        $this->enabledFilters[$field]['label']       = $label;

        if ($value == '') {
            $this->clearFilter($field);
        }
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
