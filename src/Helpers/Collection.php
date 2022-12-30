<?php

namespace PowerComponents\LivewirePowerGrid\Helpers;

use Illuminate\Container\Container;
use Illuminate\Pagination\{LengthAwarePaginator, Paginator};
use Illuminate\Support\{Carbon, Collection as BaseCollection, Str};
use PowerComponents\LivewirePowerGrid\Filters\{FilterInputText, FilterMultiSelect};

class Collection
{
    use InputOperators;

    private BaseCollection $query;

    private array $columns;

    private string $search;

    private array $filters;

    private array $inputRangeConfig = [];

    /**
     * @param BaseCollection $query
     */
    public function __construct($query)
    {
        $this->query = $query;
    }

    /**
     * @param BaseCollection $query
     * @return self
     */
    public static function query($query): self
    {
        /** @phpstan-ignore-next-line */
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
                    'date_picker'  => $this->filterDatePicker($field, $value),
                    'multi_select' => $this->filterMultiSelect($field, $value),
                    'select'       => $this->filterSelect($field, $value),
                    'boolean'      => $this->filterBoolean($field, $value),
                    'number'       => $this->filterNumber($field, $value),
                    'input_text'   => $this->filterInputText($field, $value),
                    default        => null
                };
            }
        }

        return $this->query;
    }

    private function filterDatePicker(string $field, array $value): void
    {
        if (isset($value[0]) && isset($value[1])) {
            $this->query = $this->query->whereBetween($field, [Carbon::parse($value[0]), Carbon::parse($value[1])]);
        }
    }

    private function filterInputText(string $field, ?string $value): void
    {
        $selectedOperator = $this->validateInputTextOptions($this->filters, $field);

        $this->query = FilterInputText::collection($this->query, $field, [
            'selected' => $selectedOperator,
            'value'    => $value,
        ]);
    }

    private function filterBoolean(string $field, ?string $value): void
    {
        if (is_null($value)) {
            $value = 'all';
        }

        if ($value != 'all') {
            $value = ($value == 'true');

            $this->query = $this->query->where($field, '=', $value);
        }
    }

    private function filterSelect(string $field, string $value): void
    {
        if (filled($value)) {
            $this->query = $this->query->where($field, $value);
        }
    }

    private function filterMultiSelect(string $field, array|BaseCollection $value): void
    {
        $this->query = FilterMultiSelect::collection($this->query, $field, $value);
    }

    /**
     * @param array<string> $value
     */
    private function filterNumber(string $field, array $value): void
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
