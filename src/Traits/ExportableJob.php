<?php

namespace PowerComponents\LivewirePowerGrid\Traits;

use Illuminate\Database\Eloquent as Eloquent;
use Illuminate\Support\{Collection, Str, Stringable};
use PowerComponents\LivewirePowerGrid\DataSource\Builder;
use PowerComponents\LivewirePowerGrid\{DataSource\ProcessDataSource,
    DataSource\Processors\DataSourceBase,
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

    private function getFilename(): Stringable
    {
        return Str::of($this->fileName)
            ->replace('.xlsx', '')
            ->replace('.csv', '');
    }

    private function prepareToExport(array $properties = []): Eloquent\Collection|Collection
    {
        $this->componentTable->filters  = $this->filters ?? [];
        $this->componentTable->filtered = $this->filtered ?? [];

        $processDataSource = tap(
            ProcessDataSource::make($this->componentTable, $properties),
            fn ($datasource) => $datasource->get()
        );

        $filtered = $processDataSource->component->filtered ?? [];

        $currentTable = $processDataSource->component->currentTable;

        $sortField = Str::of($processDataSource->component->sortField)->contains('.') ? $processDataSource->component->sortField : $currentTable . '.' . $processDataSource->component->sortField;

        $results = $this->componentTable->datasource($this->properties ?? []) // @phpstan-ignore-line
            ->where(
                fn ($query) => Builder::make($query, $this->componentTable)
                    ->filterContains()
                    ->filter()
            )
            ->when($filtered, function ($query, $filtered) use ($processDataSource) {
                return $query->whereIn($processDataSource->component->primaryKey, $filtered);
            })
            ->offset($this->offset)
            ->limit($this->limit)
            ->orderBy($sortField, $processDataSource->component->sortDirection)
            ->get();

        return DataSourceBase::transform($results, $this->componentTable);
    }
}
