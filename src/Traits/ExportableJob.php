<?php

namespace PowerComponents\LivewirePowerGrid\Traits;

use Illuminate\Database\Eloquent as Eloquent;
use Illuminate\Support\{Collection, Str, Stringable};
use PowerComponents\LivewirePowerGrid\DataSource\Builder;
use PowerComponents\LivewirePowerGrid\{PowerGridComponent, ProcessDataSource};

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

    private function getFilename(): Stringable
    {
        return Str::of($this->fileName)
            ->replace('.xlsx', '')
            ->replace('.csv', '');
    }

    private function prepareToExport(array $properties = []): Eloquent\Collection|Collection
    {
        /** @phpstan-ignore-next-line */
        $processDataSource = tap(ProcessDataSource::fillData($this->componentTable, $properties), fn ($datasource) => $datasource->get());

        $inClause = $processDataSource->component->filtered ?? [];

        /** @phpstan-ignore-next-line */
        $this->componentTable->filters = $this->filters ?? [];

        /** @phpstan-ignore-next-line */
        $currentTable = $processDataSource->component->currentTable;

        $sortField = Str::of($processDataSource->component->sortField)->contains('.') ? $processDataSource->component->sortField : $currentTable . '.' . $processDataSource->component->sortField;

        $results = $processDataSource->prepareDataSource() // @phpstan-ignore-line
            ->where(
                fn ($query) => Builder::make($query, $this->componentTable)
                    ->filterContains()
                    ->filter()
            )
            ->when($inClause, function ($query, $inClause) use ($processDataSource) {
                return $query->whereIn($processDataSource->component->primaryKey, $inClause);
            })
            ->orderBy($sortField, $processDataSource->component->sortDirection)
            ->get();

        return $processDataSource->transform($results, $this->componentTable);
    }
}
