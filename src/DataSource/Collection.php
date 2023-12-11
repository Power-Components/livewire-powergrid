<?php

namespace PowerComponents\LivewirePowerGrid\DataSource;

use Illuminate\Container\Container;
use Illuminate\Pagination\{LengthAwarePaginator, Paginator};
use Illuminate\Support\{Collection as BaseCollection, Str};
use PowerComponents\LivewirePowerGrid\Components\Filters\Builders\{Boolean,
    DatePicker,
    DateTimePicker,
    InputText,
    MultiSelect,
    Select};
use PowerComponents\LivewirePowerGrid\Components\Filters\{Builders\Number};
use PowerComponents\LivewirePowerGrid\DataSource\Support\InputOperators;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

class Collection
{
    use InputOperators;

    public function __construct(
        private BaseCollection              $collection,
        private readonly PowerGridComponent $powerGridComponent
    ) {
    }

    public static function make(
        BaseCollection $collection,
        PowerGridComponent $powerGridComponent
    ): self {
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
        $searchTerm = strtolower($this->powerGridComponent->search);

        if (empty($searchTerm)) {
            return $this->collection;
        }

        $this->collection = $this->collection->filter(function ($row) use ($searchTerm) {
            foreach ($this->powerGridComponent->columns as $column) {
                $field = $column->field;

                if (Str::contains(strtolower($row->{$field}), $searchTerm)) {
                    return stristr($row->{$field}, $searchTerm) !== false;
                }
            }

            return false;
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
                    ->filter(fn ($filter) => data_get($filter, 'column') === $field)
                    ->first();

                $this->collection = match ($filterType) {
                    'datetime'     => (new DateTimePicker($this->powerGridComponent, $filter))->collection($this->collection, $field, $value),
                    'date'         => (new DatePicker($this->powerGridComponent, $filter))->collection($this->collection, $field, $value),
                    'multi_select' => (new MultiSelect($this->powerGridComponent, $filter))->collection($this->collection, $field, $value),
                    'select'       => (new Select($this->powerGridComponent, $filter))->collection($this->collection, $field, $value),
                    'boolean'      => (new Boolean($this->powerGridComponent, $filter))->collection($this->collection, $field, $value),
                    'number'       => (new Number($this->powerGridComponent, $filter))->collection($this->collection, $field, $value),
                    'input_text'   => (new InputText($this->powerGridComponent, $filter))->collection($this->collection, $field, [
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
        $searchTerm = strtolower($this->powerGridComponent->search);

        if (empty($searchTerm)) {
            return $this;
        }

        $this->collection = $this->collection->filter(function ($row) use ($searchTerm) {
            $row = (object) $row;

            foreach ($this->powerGridComponent->columns as $column) {
                if ($column->searchable) {
                    $field = filled(data_get($column, 'dataField')) ? data_get($column, 'dataField') : data_get($column, 'field');

                    try {
                        if (Str::contains(strtolower($row->{$field}), $searchTerm)) {
                            return stristr($row->{$field}, $searchTerm) !== false;
                        }
                    } catch (\Throwable $exception) {
                        throw new \Exception($exception);
                    }
                }
            }

            return false;
        });

        return $this;
    }
}
