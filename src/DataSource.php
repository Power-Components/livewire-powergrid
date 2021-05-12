<?php

namespace PowerComponents\LivewirePowerGrid;

use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class DataSource
{
    private string $search = '';

    private $dataSource = null;

    private array $columns = [];

    private array $filters = [];

    private int $perPage = 10;

    private $transform;

    private $id;

    public function id($id): DataSource
    {
        $this->id = $id;

        return $this;
    }

    public function dataSource($dataSource): DataSource
    {
        $this->dataSource = $dataSource;

        return $this;
    }

    public function transform($transform): DataSource
    {
        $this->transform = $transform;

        return $this;
    }

    public function columns($columns): DataSource
    {
        $this->columns = $columns;

        return $this;
    }

    public function filters($filters): DataSource
    {
        $this->filters = $filters;

        return $this;
    }

    public function perPage($perPage): DataSource
    {
        $this->perPage = $perPage;

        return $this;
    }

    /**
     * @return LengthAwarePaginator
     * @throws \Exception
     */
    public function model(): LengthAwarePaginator
    {
        $data_source = $this->dataSource;

        $table = $data_source->getModel()->getTable();

        $query = $data_source->where(function ($query) use ($table) {
            if ($this->search != '') {
                if ($query->getModel()->count() === 0) {
                    $query->where(function ($query) use ($table) {
                        foreach ($this->columns as $column) {
                            $hasColumn = Schema::hasColumn($table, $column->field);
                            if ($hasColumn) {
                                $query->orWhere($column->field, 'like', '%' . $this->search . '%');
                            }
                        }
                    });
                }
            }

            if (count($this->filters)) {
                foreach ($this->filters as $key => $type) {
                    $query->where(function ($query) use ($key, $type) {
                        foreach ($type as $field => $value) {
                            switch ($key) {
                                case 'date_picker':
                                    $this->filterDatePicker($query, $field, $value);

                                    break;
                                case 'multi_select':
                                    $this->filterMultiSelect($query, $field, $value);

                                    break;
                                case 'select':
                                    $this->filterSelect($query, $field, $value);

                                    break;
                                case 'boolean':
                                    $this->filterBoolean($query, $field, $value);

                                    break;
                                case 'input_text':
                                    $this->filterInputText($query, $field, $value);

                                    break;
                                case 'number':
                                    $this->filterNumber($query, $field, $value);

                                    break;
                            }
                        }

                        return $query;
                    });
                }
            }
            //  $this->filtered = $query->pluck('id')->toArray();
        })->paginate($this->perPage);

        $updatedItems = $query->getCollection();

        $updatedItems = $updatedItems->transform(function ($row) {
            $columns = $this->transform->columns;
            foreach ($columns as $key => $column) {
                $row->{$key} = $column($row);
            }

            return $row;
        });

        $query->setCollection($updatedItems);

        return $query;
    }

    /**
     * @throws \Exception
     */
    public function collection($cached = '')
    {
        if (filled($cached)) {
            cache()->forget($this->id);

            return cache()->rememberForever($this->id, function () use ($cached) {
                return $cached;
            });
        }

        $cache = config('livewire-powergrid.cached_data');
        if ($cache) {
            return \cache()->rememberForever($this->id, function () {
                return new Collection($this->dataSource);
            });
        }

        return new Collection($this->dataSource);
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
    private function filterDatePicker($collection, string $field, $value)
    {
        if (isset($value[0]) && isset($value[1])) {
            $collection->whereBetween($field, [Carbon::parse($value[0]), Carbon::parse($value[1])]);
        }
    }

    /**
     * @param string $field
     * @param $value
     */
    private function filterInputText($query, string $field, $value)
    {
        $textFieldOperator = ($this->validateInputTextOptions($field) ? strtolower($this->filters['input_text_options'][$field]) : 'contains');

        if ($textFieldOperator == 'is') {
            $query->where($field, '=', $value);
        }

        if ($textFieldOperator == 'is_not') {
            $query->where($field, '!=', $value);
        }

        if ($textFieldOperator == 'starts_with') {
            $query->where($field, 'like', $value . '%');
        }

        if ($textFieldOperator == 'ends_with') {
            $query->where($field, 'like', '%' . $value);
        }

        if ($textFieldOperator == 'contains') {
            $query->where($field, 'like', '%' . $value . '%');
        }

        if ($textFieldOperator == 'contains_not') {
            $query->where($field, 'not like', '%' . $value . '%');
        }
    }

    /**
     * @param string $field
     * @param $value
     */
    private function filterBoolean($collection, string $field, $value)
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
    private function filterSelect($collection, string $field, $value)
    {
        $collection->where($field, $value);
    }

    /**
     * @param string $field
     * @param $value
     */
    private function filterMultiSelect($collection, string $field, $value)
    {
        if (count(collect($value)->get('values'))) {
            $collection->whereIn($field, collect($value)->get('values'));
        }
    }

    /**
     * @param string $field
     * @param $value
     */
    private function filterNumber($collection, string $field, $value)
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
