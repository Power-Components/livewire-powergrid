<?php

namespace PowerComponents\LivewirePowerGrid\Traits;

use Illuminate\Database\Eloquent;
use Illuminate\Support\{Collection, Str, Stringable};
use PowerComponents\LivewirePowerGrid\{DataSource\DataTransformer,
    DataSource\ProcessDataSource,
    DataSource\Processors\Database\Handlers\FilterHandler,
    DataSource\Processors\Database\Handlers\SearchHandler,
    PowerGridComponent};

/** @codeCoverageIgnore */
trait ExportableJob
{
    private string $fileName;

    private PowerGridComponent $componentTable;

    private array $columns;

    private string $exportableClass;

    private int $offset;

    private int $limit;

    private array $filters;

    private array $filtered;

    private array $exportable;

    private function getFilename(): Stringable
    {
        return Str::of($this->fileName)
            ->replace('.xlsx', '')
            ->replace('.csv', '');
    }

    private function prepareToExport(array $properties = []): Eloquent\Collection|Collection
    {
        $this->componentTable->filters = $this->filters ?? [];
        $this->componentTable->filtered = $this->filtered ?? [];
        $this->componentTable->columns = $this->columns;
        $this->componentTable->search = data_get($properties, 'search', '');

        $processDataSource = tap(
            ProcessDataSource::make($this->componentTable, $properties),
            fn ($datasource) => $datasource->get()
        );

        $filtered = $processDataSource->component->filtered ?? [];
        $currentTable = $processDataSource->component->currentTable;

        $property = function (string $property) use ($processDataSource, $currentTable) {
            $property = $processDataSource->component->{$property};

            return Str::of($property)->contains('.')
                ? $property
                : $currentTable.'.'.$property;
        };

        $results = $this->componentTable->datasource($this->properties ?? []) // @phpstan-ignore-line
            ->where(function ($query) {
                (new SearchHandler($this->componentTable))->apply($query);
                (new FilterHandler($this->componentTable))->apply($query);
            })
            ->when($filtered, function ($query, $filtered) use ($property) {
                return $query->whereIn($property('primaryKey'), $filtered);
            })
            ->offset($this->offset)
            ->limit($this->limit)
            ->orderBy($property('sortField'), $processDataSource->component->sortDirection)
            ->get();

        $dataTransformer = new DataTransformer($processDataSource->component);

        return $dataTransformer->transform($results)->collection;
    }
}
