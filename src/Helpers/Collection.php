<?php

namespace PowerComponents\LivewirePowerGrid\Helpers;

use Illuminate\Container\Container;
use Illuminate\Pagination\{LengthAwarePaginator, Paginator};
use Illuminate\Support\{Carbon, Collection as BaseCollection, Facades\Schema, Str};
use PowerComponents\LivewirePowerGrid\Services\Contracts\CollectionFilterInterface;

class Collection implements CollectionFilterInterface
{
    private BaseCollection $query;

    private array $columns;

    private string $search;

    private array $filters;

    private array $inputRangeConfig = [];

    public function __construct(BaseCollection $query)
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
    public function setColumns(array $columns): self
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * @param string $search
     * @return $this
     */
    public function setSearch(string $search): self
    {
        $this->search = $search;

        return $this;
    }

    /**
     * @param array $filters
     * @return $this
     */
    public function setFilters(array $filters): self
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

    public function filterDatePicker(string $field, array $value): void
    {
        if (isset($value[0]) && isset($value[1])) {
            $this->query = $this->query->whereBetween($field, [Carbon::parse($value[0]), Carbon::parse($value[1])]);
        }
    }

    public function filterInputText(string $field, ?string $value): void
    {
        $textFieldOperator = (validateInputTextOptions($this->filters, $field) ? strtolower($this->filters['input_text_options'][$field]) : 'contains');

        switch ($textFieldOperator) {
            case 'is':
                $this->query = $this->query->where($field, '=', $value);

                break;
            case 'is_not':
                $this->query = $this->query->where($field, '!=', $value);

                break;
            case 'starts_with':
                $this->query = $this->query->filter(function ($row) use ($field, $value) {
                    $row     = (object) $row;

                    return Str::startsWith(Str::lower($row->{$field}), Str::lower((string) $value));
                });

                break;
            case 'ends_with':
                $this->query = $this->query->filter(function ($row) use ($field, $value) {
                    $row     = (object) $row;

                    return Str::endsWith(Str::lower($row->{$field}), Str::lower((string) $value));
                });

                break;
            case 'contains':
                $this->query = $this->query->filter(function ($row) use ($field, $value) {
                    $row     = (object) $row;

                    return false !== stristr($row->{$field}, strtolower((string) $value));
                });

                break;
            case 'contains_not':

                $this->query = $this->query->filter(function ($row) use ($field, $value) {
                    $row     = (object) $row;

                    return !Str::Contains(Str::lower($row->{$field}), Str::lower((string) $value));
                });

                break;

            case 'is_blank':
                $this->query = $this->query->whereNotNull($field)->where($field, '=', '');

                break;

            case 'is_not_blank':
                $this->query = $this->query->filter(function ($row) use ($field) {
                    $row     = (object) $row;

                    return $row->{$field} != '' || is_null($row->{$field});
                });

                break;

            case 'is_null':
                $this->query = $this->query->whereNull($field);

                break;

            case 'is_not_null':
                $this->query = $this->query->whereNotNull($field);

                break;

            case 'is_empty':
                $this->query = $this->query->filter(function ($row) use ($field) {
                    $row     = (object) $row;

                    return $row->{$field} == '' || is_null($row->{$field});
                });

                break;

            case 'is_not_empty':
                $this->query = $this->query->filter(function ($row) use ($field) {
                    $row     = (object) $row;

                    return $row->{$field} !== '' && !is_null($row->{$field});
                });

                break;
        }
    }

    public function filterBoolean(string $field, string $value): void
    {
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
                $row     = (object) $row;

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
