<?php

namespace PowerComponents\LivewirePowerGrid\Plugins\Export\Concerns;

use Illuminate\Database\Eloquent;
use Illuminate\Support\{Collection, LazyCollection, Str, Stringable};
use PowerComponents\LivewirePowerGrid\{Column, PowerGridComponent};
use PowerComponents\Turbine\DataSource\{DataTransformer, ProcessDataSource};
use PowerComponents\Turbine\DataSource\Processors\Database\Handlers\{FilterHandler, SearchHandlerContract};
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

        if ($datasource instanceof Collection) {
            $processDataSource->get();
        }

        $filtered = $this->componentTable->filtered;
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
            ?? $this->componentTable->sortDirection);

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
            ->when(is_string($sortField) && $this->componentTable->isValidSortField($sortField), function ($query) use ($sortField, $sortDirection) {
                return $query->orderBy(
                    $this->componentTable->resolveSortField($sortField),
                    $sortDirection
                );
            })
            ->offset($this->offset)
            ->limit($this->limit)
            ->cursor();

        $dataTransformer = new DataTransformer($processDataSource->component);

        return $dataTransformer->transformForExport($results);
    }
}
