<?php

namespace PowerComponents\LivewirePowerGrid\Http;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\{AbstractPaginator, LengthAwarePaginator};
use Illuminate\Support\Collection;
use PowerComponents\LivewirePowerGrid\Contracts\PowerGridContext;
use PowerComponents\LivewirePowerGrid\DataSource\ProcessDataSource;
use PowerComponents\LivewirePowerGrid\Support\Actions\ActionsResolver;

final class PowerGridResponse
{
    public function __construct(private readonly PowerGridContext $context) {}

    public static function make(PowerGridContext $context): self
    {
        return new self($context);
    }

    /**
     * @return array{data: list<array<string, mixed>>, meta: array<string, mixed>, columns: list<array<string, mixed>>, filters: list<array<string, mixed>>, actions: array<string, list<array<string, mixed>>>}
     */
    public function toArray(): array
    {
        $results = ProcessDataSource::make($this->context)->get()['results'];

        $items = $this->items($results);
        $primaryKey = $this->context->state()->primaryKey;
        $actionsResolver = new ActionsResolver($this->context);

        $data = [];
        $actions = [];

        foreach ($items as $item) {
            $row = is_object($item) ? $item : (object) $item;
            $data[] = $this->rowToArray($row);

            $resolved = $actionsResolver->forRow($row);

            if ($resolved !== []) {
                $key = data_get($row, $primaryKey);

                if (is_scalar($key)) {
                    $actions[(string) $key] = $resolved;
                }
            }
        }

        return [
            'data' => $data,
            'meta' => $this->meta($results),
            'columns' => $this->columnsSchema(),
            'filters' => $this->filtersSchema(),
            'actions' => $actions,
        ];
    }

    public function toResponse(): JsonResponse
    {
        return new JsonResponse($this->toArray());
    }

    /**
     * @return Collection<int, mixed>
     */
    private function items(mixed $results): Collection
    {
        if ($results instanceof AbstractPaginator) {
            /** @var Collection<int, mixed> $collection */
            $collection = $results->getCollection();

            return $collection;
        }

        if ($results instanceof Collection) {
            return $results;
        }

        $value = $results instanceof Arrayable ? $results->toArray() : $results;

        return collect(is_iterable($value) ? $value : []);
    }

    private function asString(mixed $value): string
    {
        return is_scalar($value) ? (string) $value : '';
    }

    /**
     * @return array<string, mixed>
     */
    private function rowToArray(object $row): array
    {
        $data = $row instanceof Model ? $row->toArray() : (array) $row;

        foreach (array_keys($data) as $key) {
            if (str_starts_with((string) $key, '__powergrid')) {
                unset($data[$key]);
            }
        }

        return $data;
    }

    /**
     * @return array<string, mixed>
     */
    private function meta(mixed $results): array
    {
        $state = $this->context->state();

        $pagination = [];

        if ($results instanceof AbstractPaginator) {
            $pagination = [
                'current_page' => $results->currentPage(),
                'per_page' => $results->perPage(),
                'from' => $results->firstItem(),
                'to' => $results->lastItem(),
            ];

            if ($results instanceof LengthAwarePaginator) {
                $pagination['total'] = $results->total();
                $pagination['last_page'] = $results->lastPage();
            }
        }

        return [
            'pagination' => $pagination,
            'sort' => [
                'field' => $state->sortField,
                'direction' => $state->sortDirection,
                'multiSort' => $state->multiSort,
                'sortArray' => $state->sortArray,
            ],
            'search' => $state->search,
            'filters' => $state->filters,
            'filterBuilder' => $state->filterBuilder,
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function columnsSchema(): array
    {
        $schema = [];

        foreach ($this->context->declaredColumns() as $column) {
            if (data_get($column, 'isAction') === true) {
                continue;
            }

            $field = $this->asString(data_get($column, 'dataField') ?: data_get($column, 'field'));

            if ($field === '') {
                continue;
            }

            $schema[] = [
                'field' => $field,
                'title' => $this->asString(data_get($column, 'title')),
                'sortable' => (bool) data_get($column, 'sortable'),
                'searchable' => (bool) data_get($column, 'searchable'),
                'hidden' => (bool) data_get($column, 'hidden'),
            ];
        }

        return $schema;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function filtersSchema(): array
    {
        $schema = [];

        foreach ($this->context->declaredFilters() as $filter) {
            $field = $this->asString(data_get($filter, 'field') ?: data_get($filter, 'column'));

            if ($field === '') {
                continue;
            }

            $schema[] = [
                'key' => $this->asString(data_get($filter, 'key')),
                'field' => $field,
                'column' => $this->asString(data_get($filter, 'column')),
                'title' => $this->asString(data_get($filter, 'title')),
            ];
        }

        return $schema;
    }
}
