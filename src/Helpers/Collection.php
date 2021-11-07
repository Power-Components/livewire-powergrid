<?php

namespace PowerComponents\LivewirePowerGrid\Helpers;

use Illuminate\Container\Container;
use Illuminate\Pagination\{LengthAwarePaginator, Paginator};
use Illuminate\Support\{Carbon, Collection as BaseCollection, Str};
use PowerComponents\LivewirePowerGrid\Services\Contracts\CollectionFilterInterface;

class Collection implements CollectionFilterInterface
{
    private BaseCollection $query;

    private array $columns;

    private string $search;

    private array $filters;

    /**
     * Model constructor.
     */
    public function __construct($query)
    {
        $this->query = $query;
    }

    /**
     * @param $query
     * @return Collection
     */
    public static function query($query): Collection
    {
        return new static($query);
    }

    /**
     * @param array $columns
     * @return $this
     */
    public function setColumns(array $columns): Collection
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * @param string $search
     * @return $this
     */
    public function setSearch(string $search): Collection
    {
        $this->search = $search;

        return $this;
    }

    /**
     * @param array $filters
     * @return $this
     */
    public function setFilters(array $filters): Collection
    {
        $this->filters = $filters;

        return $this;
    }

    /**
     * @param BaseCollection $results
     * @param $pageSize
     * @return LengthAwarePaginator
     */
    public static function paginate(BaseCollection $results, $pageSize): LengthAwarePaginator
    {
        $pageSize = ($pageSize == '0') ? $results->count() : $pageSize;
        $page     = Paginator::resolveCurrentPage('page');

        $total = $results->count();

        return self::paginator($results->forPage($page, $pageSize), $total, $pageSize, $page, [
            'path'     => Paginator::resolveCurrentPath(),
            'pageName' => 'page',
        ]);
    }

    /**
     * Create a new length-aware paginator instance.
     *
     * @param BaseCollection $items
     * @param int $total
     * @param int $perPage
     * @param int $currentPage
     * @param array $options
     * @return LengthAwarePaginator
     */
    protected static function paginator($items, $total, $perPage, $currentPage, $options): LengthAwarePaginator
    {
        return Container::getInstance()->makeWith(LengthAwarePaginator::class, compact(
            'items',
            'total',
            'perPage',
            'currentPage',
            'options'
        ));
    }

    /**
     * @return BaseCollection
     */
    public function search(): BaseCollection
    {
        if (!empty($this->search)) {
            $this->query = $this->query->filter(function ($row) {
                foreach ($this->columns as $column) {
                    $field = $column->field;
                    if (Str::contains(strtolower($row->$field), strtolower($this->search))) {
                        return false !== stristr($row->$field, strtolower($this->search));
                    }
                }
            });
        }

        return $this->query;
    }

    /**
     * @return BaseCollection
     */
    public function filter(): BaseCollection
    {
        if (count($this->filters) === 0) {
            return $this->query;
        }

        foreach ($this->filters as $key => $type) {
            foreach ($type as $field => $value) {
                if (!filled($value)) {
                    continue;
                }
                switch ($key) {
                    case 'date_picker':
                        $this->filterDatePicker($field, $value);

                        break;
                    case 'multi_select':
                        $this->filterMultiSelect($field, $value);

                        break;
                    case 'select':
                        $this->filterSelect($field, $value);

                        break;
                    case 'boolean':
                        $this->filterBoolean($field, $value);

                        break;
                    case 'input_text':
                        $this->filterInputText($field, $value);

                        break;
                    case 'number':
                        $this->filterNumber($field, $value);

                        break;
                }
            }
        }

        return $this->query;
    }

    /**
     * @param string $field
     * @param $value
     * @return void
     */
    public function filterDatePicker(string $field, $value)
    {
        if (isset($value[0]) && isset($value[1])) {
            $this->query = $this->query->whereBetween($field, [Carbon::parse($value[0]), Carbon::parse($value[1])]);
        }
    }

    /**
     * Validate if the given value is valid as an Input Option
     *
     * @param string $field Field to be checked
     * @return bool
     */
    private function validateInputTextOptions(string $field): bool
    {
        return isset($this->filters['input_text_options'][$field]) && in_array(
            strtolower($this->filters['input_text_options'][$field]),
            ['is', 'is_not', 'contains', 'contains_not', 'starts_with', 'ends_with']
        );
    }

    /**
     * @param string $field
     * @param $value
     * @return void
     */
    public function filterInputText(string $field, $value): void
    {
        $textFieldOperator = ($this->validateInputTextOptions($field) ? strtolower($this->filters['input_text_options'][$field]) : 'contains');

        if ($textFieldOperator == 'is') {
            $this->query = $this->query->where($field, '=', $value);
        }

        if ($textFieldOperator == 'is_not') {
            $this->query = $this->query->where($field, '!=', $value);
        }

        if ($textFieldOperator == 'starts_with') {
            $this->query = $this->query->filter(function ($row) use ($field, $value) {
                return Str::startsWith(Str::lower($row->$field), Str::lower($value));
            });
        }

        if ($textFieldOperator == 'ends_with') {
            $this->query = $this->query->filter(function ($row) use ($field, $value) {
                return Str::endsWith(Str::lower($row->$field), Str::lower($value));
            });
        }

        if ($textFieldOperator == 'contains') {
            $this->query = $this->query->filter(function ($row) use ($field, $value) {
                return false !== stristr($row->$field, strtolower($value));
            });
        }

        if ($textFieldOperator == 'contains_not') {
            $this->query = $this->query->filter(function ($row) use ($field, $value) {
                return !Str::Contains(Str::lower($row->$field), Str::lower($value));
            });
        }
    }

    /**
     * @param string $field
     * @param $value
     * @return void
     */
    public function filterBoolean(string $field, $value): void
    {
        if ($value != 'all') {
            $value = ($value == 'true');

            $this->query = $this->query->where($field, '=', $value);
        }
    }

    /**
     * @param string $field
     * @param $value
     */
    public function filterSelect(string $field, $value): void
    {
        if (filled($value)) {
            $this->query = $this->query->where($field, $value);
        }
    }

    /**
     * @param string $field
     * @param $value
     * @return mixed
     */
    public function filterMultiSelect(string $field, $value): void
    {
        $empty  = false;
        $values = collect($value)->get('values');
        if (count($values) > 0) {
            foreach ($values as $value) {
                if ($value === '') {
                    $empty = true;
                }
            }
            if (!$empty) {
                $this->query = $this->query->whereIn($field, $values);
            }
        }
    }

    /**
     * @param string $field
     * @param $value
     */
    public function filterNumber(string $field, $value): void
    {
        if (isset($value['start']) && !isset($value['end'])) {
            $start = str_replace($value['thousands'], '', $value['start']);
            $start = (float) str_replace($value['decimal'], '.', $start);

            $this->query = $this->query->where($field, '>=', $start);
        }
        if (!isset($value['start']) && isset($value['end'])) {
            $end = str_replace($value['thousands'], '', $value['end']);
            $end = (float) str_replace($value['decimal'], '.', $end);

            $this->query = $this->query->where($field, '<=', $end);
        }
        if (isset($value['start']) && isset($value['end'])) {
            $start = str_replace($value['thousands'], '', $value['start']);
            $start = str_replace($value['decimal'], '.', $start);

            $end = str_replace($value['thousands'], '', $value['end']);
            $end = str_replace($value['decimal'], '.', $end);

            $this->query = $this->query->whereBetween($field, [$start, $end]);
        }
    }

    /**
     * @return void
     */
    public function filterContains(): Collection
    {
        if (!empty($this->search)) {
            $this->query = $this->query->filter(function ($row) {
                $row = (object) $row;

                foreach ($this->columns as $column) {
                    $field = $column->field;

                    try {
                        if (Str::contains(strtolower($row->{$field}), strtolower($this->search))) {
                            return false !== stristr($row->{$field}, strtolower($this->search));
                        }
                    } catch (\Exception $exception) {
                        throw new \Exception($exception);
                    }
                }

                return false;
            });
        }

        return $this;
    }
}
