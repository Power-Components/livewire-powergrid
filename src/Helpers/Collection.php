<?php

namespace PowerComponents\LivewirePowerGrid\Helpers;

use Illuminate\Container\Container;
use Illuminate\Pagination\{LengthAwarePaginator, Paginator};
use Illuminate\Support\{Carbon, Collection as BaseCollection, Str};

class Collection
{
    use InputOperators;

    private BaseCollection $query;

    private array $columns;

    private string $search;

    private array $filters;

    private array $inputRangeConfig = [];

    public function __construct(BaseCollection $query)
    {
        $this->query = $query;
    }

    public static function query(BaseCollection $query): Collection
    {
        return new Collection($query);
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

    public function setInputTextOperators(array $operators): Collection
    {
        $this->inputTextOperators = $operators;

        return $this;
    }

    public static function paginate(BaseCollection $results, int $pageSize): LengthAwarePaginator
    {
        $pageSize = ($pageSize == '0') ? $results->count() : $pageSize;
        $page     = Paginator::resolveCurrentPage('page');

        $total = $results->count();

        return self::paginator($results->forPage($page, $pageSize), $total, $pageSize, $page, [
            'path'     => Paginator::resolveCurrentPath(),
            'pageName' => 'page',
        ]);
    }

    protected static function paginator(BaseCollection $items, int $total, int $perPage, int $currentPage, array $options): LengthAwarePaginator
    {
        /** @var LengthAwarePaginator $paginator */
        $paginator = Container::getInstance()->makeWith(LengthAwarePaginator::class, compact(
            'items',
            'total',
            'perPage',
            'currentPage',
            'options'
        ));

        return $paginator;
    }

    public function search(): BaseCollection
    {
        if (!empty($this->search)) {
            $this->query = $this->query->filter(function ($row) {
                foreach ($this->columns as $column) {
                    $field = $column->field;

                    if (Str::contains(strtolower($row->{$field}), strtolower($this->search))) {
                        return false !== stristr($row->{$field}, strtolower($this->search));
                    }
                }
            });
        }

        return $this->query;
    }

    public function filter(): BaseCollection
    {
        if (count($this->filters) === 0) {
            return $this->query;
        }

        foreach ($this->filters as $key => $type) {
            foreach ($type as $field => $value) {
                match ($key) {
                    'date_picker'         => $this->filterDatePicker($field, $value),
                    'multi_select'        => $this->filterMultiSelect($field, $value),
                    'select'              => $this->filterSelect($field, $value),
                    'boolean'             => $this->filterBoolean($field, $value),
                    'input_text_contains' => $this->filterInputTextContains($field, $value),
                    'number'              => $this->filterNumber($field, $value),
                    'input_text'          => $this->filterInputText($field, $value),
                    default               => null
                };
            }
        }

        return $this->query;
    }

    public function filterDatePicker(string $field, array $value): void
    {
        if (isset($value[0]) && isset($value[1])) {
            $this->query = $this->query->whereBetween($field, [Carbon::parse($value[0]), Carbon::parse($value[1])]);
        }
    }

    public function filterInputTextContains(string $field, ?string $value): void
    {
        $this->query = $this->query->filter(function ($row) use ($field, $value) {
            $row = (object) $row;

            return false !== stristr($row->{$field}, strtolower((string) $value));
        });
    }

    public function filterInputText(string $field, ?string $value): void
    {
        $textFieldOperator = $this->validateInputTextOptions($this->filters, $field);

        match ($textFieldOperator) {
            'is'           => $this->query->where($field, '=', $value),
            'is_not'       => $this->query->where($field, '!=', $value),
            'starts_with'  => $this->query->filter(function ($row) use ($field, $value) {
                $row = (object) $row;

                return Str::startsWith(Str::lower($row->{$field}), Str::lower((string) $value));
            }),
            'ends_with'    => $this->query->filter(function ($row) use ($field, $value) {
                $row = (object) $row;

                return Str::endsWith(Str::lower($row->{$field}), Str::lower((string) $value));
            }),
            'contains'     => $this->query->filter(function ($row) use ($field, $value) {
                $row = (object) $row;

                return false !== stristr($row->{$field}, strtolower((string) $value));
            }),
            'contains_not' => $this->query->filter(function ($row) use ($field, $value) {
                $row = (object) $row;

                return !Str::Contains(Str::lower($row->{$field}), Str::lower((string) $value));
            }),
            'is_empty'     => $this->query->filter(function ($row) use ($field) {
                $row = (object) $row;

                return $row->{$field} == '' || is_null($row->{$field});
            }),
            'is_not_empty' => $this->query->whereNotNull($field),
            'is_null'      => $this->query->whereNull($field),
            'is_not_null'  => $this->query->filter(function ($row) use ($field) {
                $row = (object) $row;

                return $row->{$field} !== '' && !is_null($row->{$field});
            }),
            'is_blank'     => $this->query->whereNotNull($field)->where($field, '=', ''),
            'is_not_blank' => $this->query->filter(function ($row) use ($field) {
                $row = (object) $row;

                return $row->{$field} != '' || is_null($row->{$field});
            }),
            default        => null,
        };
    }

    public function filterBoolean(string $field, ?string $value): void
    {
        if (is_null($value)) {
            $value = 'all';
        }

        if ($value != 'all') {
            $value = ($value == 'true');

            $this->query = $this->query->where($field, '=', $value);
        }
    }

    public function filterSelect(string $field, string $value): void
    {
        if (filled($value)) {
            $this->query = $this->query->where($field, $value);
        }
    }

    public function filterMultiSelect(string $field, array|BaseCollection $value): void
    {
        $empty = false;
        /** @var array|null $values */
        $values = collect($value)->get('values');

        if (is_array($values) && count($values) > 0) {
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
     * @param array<string> $value
     */
    public function filterNumber(string $field, array $value): void
    {
        if (isset($value['start']) && !isset($value['end'])) {
            $start = $value['start'];

            if (isset($this->inputRangeConfig[$field])) {
                $start = str_replace($value['thousands'], '', $value['start']);
                $start = (float) str_replace($value['decimal'], '.', $start);
            }

            $this->query = $this->query->where($field, '>=', $start);
        }

        if (!isset($value['start']) && isset($value['end'])) {
            $end = $value['end'];

            if (isset($this->inputRangeConfig[$field])) {
                $end = str_replace($value['thousands'], '', $value['end']);
                $end = (float) str_replace($value['decimal'], '.', $end);
            }
            $this->query = $this->query->where($field, '<=', $end);
        }

        if (isset($value['start']) && isset($value['end'])) {
            $start = $value['start'];
            $end   = $value['end'];

            if (isset($this->inputRangeConfig[$field])) {
                $start = str_replace($value['thousands'], '', $value['start']);
                $start = str_replace($value['decimal'], '.', $start);

                $end = str_replace($value['thousands'], '', $value['end']);
                $end = str_replace($value['decimal'], '.', $end);
            }

            $this->query = $this->query->whereBetween($field, [$start, $end]);
        }
    }

    public function filterContains(): Collection
    {
        if (!empty($this->search)) {
            $this->query = $this->query->filter(function ($row) {
                $row = (object) $row;

                foreach ($this->columns as $column) {
                    if ($column->searchable) {
                        if (filled($column->dataField)) {
                            $field = $column->dataField;
                        } else {
                            $field = $column->field;
                        }

                        try {
                            if (Str::contains(strtolower($row->{$field}), strtolower($this->search))) {
                                return false !== stristr($row->{$field}, strtolower($this->search));
                            }
                        } catch (\Exception $exception) {
                            throw new \Exception($exception);
                        }
                    }
                }

                return false;
            });
        }

        return $this;
    }
}
