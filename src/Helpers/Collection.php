<?php

namespace PowerComponents\LivewirePowerGrid\Helpers;

use Illuminate\Container\Container;
use Illuminate\Pagination\{LengthAwarePaginator, Paginator};
use Illuminate\Support\{Carbon, Collection as BaseCollection, Str};
use PowerComponents\LivewirePowerGrid\Filters\{FilterBoolean,
    FilterDatePicker,
    FilterInputText,
    FilterMultiSelect,
    FilterNumber,
    FilterSelect};

class Collection
{
    use InputOperators;

    private array $columns;

    private string $search;

    private array $filters;

    /**
     * @param BaseCollection $query
     */
    public function __construct(
        private BaseCollection $query
    ) {
    }

    /**
     * @param BaseCollection $query
     * @return self
     */
    public static function query(BaseCollection $query): self
    {
        return new Collection($query);
    }

    public function setColumns(array $columns): Collection
    {
        $this->columns = $columns;

        return $this;
    }

    public function setSearch(string $search): Collection
    {
        $this->search = $search;

        return $this;
    }

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
        if (empty($this->search)) {
            return $this->query;
        }

        $this->query = $this->query->filter(function ($row) {
            foreach ($this->columns as $column) {
                $field = $column->field;

                if (Str::contains(strtolower($row->{$field}), strtolower($this->search))) {
                    return false !== stristr($row->{$field}, strtolower($this->search));
                }
            }
        });

        return $this->query;
    }

    public function filter(): BaseCollection
    {
        if (blank($this->filters)) {
            return $this->query;
        }

        foreach ($this->filters as $key => $type) {
            foreach ($type as $field => $value) {
                $this->query = match ($key) {
                    'date_picker'  => FilterDatePicker::collection($this->query, $field, $value),
                    'multi_select' => FilterMultiSelect::collection($this->query, $field, $value),
                    'select'       => FilterSelect::collection($this->query, $field, $value),
                    'boolean'      => FilterBoolean::collection($this->query, $field, $value),
                    'number'       => FilterNumber::collection($this->query, $field, $value),
                    'input_text'   => FilterInputText::collection($this->query, $field, [
                        'selected' => $this->validateInputTextOptions($this->filters, $field),
                        'value'    => $value,
                    ]),
                    default        => $this->query
                };
            }
        }

        return $this->query;
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
