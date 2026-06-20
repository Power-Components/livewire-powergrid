<?php

namespace PowerComponents\LivewirePowerGrid\Traits;

use Illuminate\Database\Eloquent;
use Illuminate\Support\{Collection, Str, Stringable};
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\{DataSource\DataTransformer,
    DataSource\ProcessDataSource,
    DataSource\Processors\Database\Handlers\FilterHandler,
    DataSource\Processors\Database\Handlers\SearchHandlerContract,
    PowerGridComponent};

/** @codeCoverageIgnore */
trait ExportableJob
{
    private string $fileName;

    private PowerGridComponent $componentTable;

    /** @var array<int, Column> */
    private array $columns;

    private string $exportableClass;

    private int $offset;

    private int $limit;

    /** @var array<string, mixed> */
    private array $filters;

    /** @var list<int|string> */
    private array $filtered;

    /** @var array<string, mixed> */
    private array $exportable;

    private function getFilename(): Stringable
    {
        return Str::of($this->fileName)
            ->replace('.xlsx', '')
            ->replace('.csv', '');
    }

    /** @param  array<string, mixed>  $properties */
    private function prepareToExport(array $properties = []): Eloquent\Collection|Collection
    {
        /** @phpstan-ignore assign.propertyType */
        $this->componentTable->filters = $this->filters ?? [];
        $this->componentTable->filtered = $this->filtered ?? [];
        $this->componentTable->columns = array_values($this->columns);
        $this->componentTable->search = strval(data_get($properties, 'search', ''));

        $processDataSource = tap(
            ProcessDataSource::make($this->componentTable, $properties),
            fn ($datasource) => $datasource->get()
        );

        $filtered = $processDataSource->component->filtered ?? [];
        $currentTable = $processDataSource->component->currentTable;

        /** @var array{sortField?: string, sortDirection?: string} $queryOptions */
        $queryOptions = data_get($this->exportable, 'queryOptions', []);

        // data_get's default only applies when the key is missing, so guard against malformed query options.
        if (! is_array($queryOptions)) {
            $queryOptions = [];
        }

        $property = function (string $property) use ($processDataSource, $currentTable) {
            $property = $processDataSource->component->{$property};

            return Str::of($property)->contains('.')
                ? $property
                : $currentTable.'.'.$property;
        };

        $sortField = $queryOptions['sortField']
            ?? $processDataSource->component->resolveSortField($processDataSource->component->sortField);

        $sortDirection = $queryOptions['sortDirection']
            ?? $processDataSource->component->sortDirection;

        $results = $processDataSource->datasource
            ->where(function ($query) {
                app()->makeWith(SearchHandlerContract::class, [
                    'component' => $this->componentTable,
                ])->apply($query);
                (new FilterHandler($this->componentTable))->apply($query);
            })
            ->when($filtered, function ($query, $filtered) use ($property) {
                return $query->whereIn($property('primaryKey'), $filtered);
            })
            ->offset($this->offset)
            ->limit($this->limit)
            ->orderBy($sortField, $sortDirection)
            ->get();

        $dataTransformer = new DataTransformer($processDataSource->component);

        return $dataTransformer->transform($results)->collection;
    }
}
