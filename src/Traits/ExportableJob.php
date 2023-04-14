<?php

namespace PowerComponents\LivewirePowerGrid\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\{Str, Stringable};
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

/** @codeCoverageIgnore */
trait ExportableJob
{
    private string $fileName;

    private PowerGridComponent $componentTable;

    private array $columns;

    private string $exportableClass;

    private int $offset;

    private int $limit;

    private function getFilename(): Stringable
    {
        return Str::of($this->fileName)
            ->replace('.xlsx', '')
            ->replace('.csv', '');
    }

    private function transform(Collection $collection): Collection
    {
        return $collection->transform(function ($row) {
            $columns = $this->componentTable->addColumns()->columns;

            foreach ($columns as $key => $column) {
                $row->{$key} = $column($row);
            }

            return $row;
        });
    }
}
