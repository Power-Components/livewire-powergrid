<?php

namespace PowerComponents\LivewirePowerGrid\Plugins\Export\Concerns;

use Illuminate\Database\Eloquent;
use Illuminate\Database\Eloquent\{Builder, Model};
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\{Collection, LazyCollection, Str, Stringable};
use PowerComponents\LivewirePowerGrid\{Column, PowerGridComponent};
use PowerComponents\Turbine\DataSource\{DataTransformer, ProcessDataSource};
use PowerComponents\Turbine\DataSource\Processors\Database\Pipelines;
use PowerComponents\Turbine\DataSource\Support\Sql;

/** @codeCoverageIgnore */
trait ExportableJob
{
    private string $fileName;

    /** @var PowerGridComponent */
    private object $componentTable;

    /** @var array<int, Column> */
    private array $columns;

    private string $exportableClass;

    private int $offset;

    private int $limit;

    /** @var array<mixed, mixed> */
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

    /**
     * @param  array<string, mixed>  $properties
     * @return Eloquent\Collection<int, mixed>|Collection<int, mixed>|LazyCollection<int, mixed>
     */
    private function prepareToExport(array $properties = []): Eloquent\Collection|Collection|LazyCollection
    {
        /** @phpstan-ignore assign.propertyType */
        $this->componentTable->filters = $this->filters ?? [];
        $this->componentTable->filtered = $this->filtered ?? [];
        $this->componentTable->columns = array_values($this->columns);
        /** @var string $search */
        $search = data_get($properties, 'search', '');
        $this->componentTable->search = $search;

        $processDataSource = ProcessDataSource::make($this->componentTable, $properties);
        $datasource = $processDataSource->resolveDatasource();
        $dataTransformer = new DataTransformer($processDataSource->component);
        $filtered = $this->componentTable->filtered;

        if ($datasource instanceof Collection || is_array($datasource)) {
            /** @var mixed $results */
            $results = data_get($processDataSource->get(isExport: true), 'results');

            if (is_object($results) && method_exists($results, 'getCollection')) {
                $results = $results->getCollection();
            }

            /** @var Collection<int, mixed> $collection */
            $collection = collect(is_iterable($results) ? $results : [])
                ->when($filtered, fn ($collection, $filtered) => $collection->whereIn($this->componentTable->primaryKey, $filtered))
                ->slice($this->offset, $this->limit)
                ->values();

            return $dataTransformer->transform($collection)->collection;
        }

        $currentTable = $this->componentTable->currentTable;

        /** @var array{sortField?: string, sortDirection?: string} $queryOptions */
        $queryOptions = data_get($this->exportable, 'queryOptions', []);

        // data_get's default only applies when the key is missing, so guard against malformed query options.
        if (! is_array($queryOptions)) {
            $queryOptions = [];
        }

        $property = function (string $property) use ($currentTable) {
            $property = $this->componentTable->{$property};

            return Str::of($property)->contains('.')
                ? $property
                : $currentTable.'.'.$property;
        };

        $sortField = $queryOptions['sortField']
            ?? $this->componentTable->sortField;
        $sortDirection = Sql::sanitizeSortDirection($queryOptions['sortDirection']
            ?? $this->componentTable->sortDirection) === 'desc' ? 'desc' : 'asc';

        // Same scoping as the grid and the sync export, so a chunk never holds rows the user cannot see.
        /** @var Builder<Model> $query */
        $query = app(Pipeline::class)
            ->send($processDataSource->datasource)
            ->through([
                new Pipelines\Filters($this->componentTable),
                new Pipelines\SoftDeletes($this->componentTable),
                new Pipelines\ColumnRawQueries($this->componentTable),
            ])
            ->thenReturn();

        $query = $query
            ->when($filtered, function ($query, $filtered) use ($property) {
                return $query->whereIn($property('primaryKey'), $filtered);
            })
            ->when(is_string($sortField) && $this->componentTable->isValidSortField($sortField), function ($query) use ($sortField, $sortDirection) {
                return $query->orderBy(
                    $this->componentTable->resolveSortField($sortField),
                    $sortDirection
                );
            })
            // Chunks are cut with offset/limit, so they need a total order to not repeat or skip rows.
            ->orderBy($property('primaryKey'));

        /** @var Builder<Model> $scoped */
        $scoped = $this->componentTable->transformQuery($query);

        $results = $scoped
            ->offset($this->offset)
            ->limit($this->limit)
            ->cursor();

        return $dataTransformer->transformForExport($results);
    }
}
