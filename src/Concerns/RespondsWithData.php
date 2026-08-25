<?php

namespace PowerComponents\LivewirePowerGrid\Concerns;

use Illuminate\Http\{JsonResponse, Request};
use PowerComponents\LivewirePowerGrid\Support\Actions\ActionsResolver;
use PowerComponents\Turbine\DataSource\Support\Sql;
use PowerComponents\Turbine\Response;
use PowerComponents\Turbine\Response\GridResponse;

trait RespondsWithData
{
    public function toDataArray(?Request $request = null): GridResponse
    {
        $this->prepareForData($request ?? request());

        return Response::make($this)->envelope(
            actionsResolver: new ActionsResolver($this),
        );
    }

    public function toDataResponse(?Request $request = null): JsonResponse
    {
        return new JsonResponse($this->toDataArray($request));
    }

    protected function prepareForData(Request $request): void
    {
        foreach ($this->setUp() as $setUp) {
            $name = is_object($setUp) ? data_get($setUp, 'name') : null;

            if (is_string($name)) {
                $this->setUp[$name] = $setUp;
            }
        }

        $this->columns = $this->columns();

        $this->applyDefaultFilters();

        /** @var array<string, mixed> $payload */
        $payload = (array) ($request->input('powergrid') ?? $request->input('turbine', []));

        $search = $payload['search'] ?? null;

        if (is_scalar($search)) {
            $this->search = (string) $search;
        }

        $sortField = $payload['sortField'] ?? null;

        if (is_scalar($sortField)) {
            $this->sortField = (string) $sortField;
        }

        $sortDirection = $payload['sortDirection'] ?? null;

        if (is_scalar($sortDirection)) {
            $this->sortDirection = Sql::sanitizeSortDirection((string) $sortDirection);
        }

        $rawFilters = $payload['filters'] ?? null;

        if (is_array($rawFilters)) {
            $filters = [];

            /** @var mixed $columns */
            foreach ($rawFilters as $type => $columns) {
                $bucket = [];

                if (is_array($columns)) {
                    /** @var mixed $value */
                    foreach ($columns as $field => $value) {
                        $bucket[(string) $field] = $value;
                    }
                }

                $filters[(string) $type] = $bucket;
            }

            $this->filters = $filters;
        }

        $this->readyToLoad = true;
    }
}
