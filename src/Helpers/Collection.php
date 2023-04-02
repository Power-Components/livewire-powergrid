<?php

namespace PowerComponents\LivewirePowerGrid\Helpers;

use Illuminate\Container\Container;
use Illuminate\Pagination\{LengthAwarePaginator, Paginator};
use Illuminate\Support\{Carbon, Collection as BaseCollection, Str};
use PowerComponents\LivewirePowerGrid\Filters\{Builders\Boolean,
    Builders\DatePicker,
    Builders\DateTimePicker,
    Builders\InputText,
    Builders\MultiSelect,
    Builders\Number,
    Builders\Select};
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

class Collection
{
    use InputOperators;

    public function __construct(private BaseCollection $collection, private PowerGridComponent $powerGridComponent)
    {
    }

    public static function make(BaseCollection $collection, PowerGridComponent $powerGridComponent): self
    {
        return new Collection($collection, $powerGridComponent);
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
            return $this->collection;
        }

        $this->collection = $this->collection->filter(function ($row) {
            foreach ($this->powerGridComponent->columns as $column) {
                $field = $column->field;

                if (Str::contains(strtolower($row->{$field}), strtolower($this->powerGridComponent->search))) {
                    return false !== stristr($row->{$field}, strtolower($this->powerGridComponent->search));
                }
            }
        });

        return $this->collection;
    }

    public function filter(): BaseCollection
    {
        if (blank($this->powerGridComponent->filters)) {
            return $this->collection;
        }

        $filters = collect($this->powerGridComponent->filters());

        if (blank($filters->flatten()->values())) {
            return $this->collection;
        }

        foreach ($this->powerGridComponent->filters as $filterType => $column) {
            foreach ($column as $field => $value) {
                $filter = collect($filters)
                    ->filter(fn ($filter) => $filter->column === $field)
                    ->first();

                $this->collection = match ($filterType) {
                    'datetime'     => (new DateTimePicker($filter))->collection($this->collection, $field, $value),
                    'date'         => (new DatePicker($filter))->collection($this->collection, $field, $value),
                    'multi_select' => (new MultiSelect($filter))->collection($this->collection, $field, $value),
                    'select'       => (new Select($filter))->collection($this->collection, $field, $value),
                    'boolean'      => (new Boolean($filter))->collection($this->collection, $field, $value),
                    'number'       => (new Number($filter))->collection($this->collection, $field, $value),
                    'input_text'   => (new InputText($filter))->collection($this->collection, $field, [
                        'selected' => $this->validateInputTextOptions($this->powerGridComponent->filters, $field),
                        'value'    => $value,
                    ]),
                    default => $this->collection
                };
            }
        }

        return $this->collection;
    }

    public function filterContains(): Collection
    {
        if (!empty($this->search)) {
            $this->collection = $this->collection->filter(function ($row) {
                $row = (object) $row;

                foreach ($this->powerGridComponent->columns as $column) {
                    if ($column->searchable) {
                        $field = filled($column->dataField) ? $column->dataField : $column->field;

                        try {
                            if (Str::contains(strtolower($row->{$field}), strtolower($this->powerGridComponent->search))) {
                                return false !== stristr($row->{$field}, strtolower($this->powerGridComponent->search));
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
