<?php

namespace PowerComponents\LivewirePowerGrid\Traits;

use Illuminate\Bus\Batch;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\{Collection as BaseCollection, Str};
use PowerComponents\LivewirePowerGrid\Services\Spout\{ExportToCsv, ExportToXLS};
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * @property ?Batch $exportBatch
 */
trait Exportable
{
    public bool $exportActive = false;

    public array $exportOptions = [];

    public string $exportFileName = 'download';

    public array $exportType = [];

    /**
     * @throws \Exception
     * @return Collection|BaseCollection
     */
    public function prepareToExport(bool $selected = false)
    {
        $inClause = $this->filtered;

        if ($selected && filled($this->checkboxValues)) {
            $inClause = $this->checkboxValues;
        }

        if ($this->isCollection) {
            if ($inClause) {
                $results = $this->resolveCollection()->whereIn($this->primaryKey, $inClause);

                return $this->transform($results);
            }

            return $this->transform($this->resolveCollection());
        }

        $model        = $this->resolveModel();
        /** @phpstan-ignore-next-line */
        $currentTable = $model->getModel()->getTable();

        if (Str::of($this->sortField)->contains('.')) {
            $sortField = $this->sortField;
        } else {
            $sortField = $currentTable . '.' . $this->sortField;
        }

        $results = $this->resolveModel()
            ->when($inClause, function ($query, $inClause) {
                return $query->whereIn($this->primaryKey, $inClause);
            })
            ->orderBy($sortField, $this->sortDirection)
            ->get();

        return $this->transform($results);
    }

    /**
     * @throws \Exception | \Throwable
     * @return BinaryFileResponse | bool
     */
    public function exportToXLS(bool $selected = false)
    {
        return $this->export(ExportToXLS::class, $selected);
    }

    /**
     * @throws \Exception | \Throwable
     * @return BinaryFileResponse | bool
     */
    public function exportToCsv(bool $selected = false)
    {
        return $this->export(ExportToCsv::class, $selected);
    }

    /**
     * @throws \Exception | \Throwable
     * @return BinaryFileResponse | bool
     */
    private function export(string $exportableClass, bool $selected)
    {
        if ($this->queues > 0 && !$selected) {
            return $this->runOnQueue($exportableClass);
        }

        if (count($this->checkboxValues) === 0 && $selected) {
            return false;
        }
        /**
         * @var ExportToCsv|ExportToCsv $exportable
         */
        $exportable = new $exportableClass();

        $currentHiddenStates = collect($this->columns)
            ->mapWithKeys(fn ($column) => [$column['field'] => $column['hidden']]);
        $columnsWithHiddenState = array_map(function ($column) use ($currentHiddenStates) {
            $column->hidden = $currentHiddenStates[$column->field];

            return $column;
        }, $this->columns());

        $exportable
            ->fileName($this->exportFileName)
            ->setData($columnsWithHiddenState, $this->prepareToExport($selected));

        return $exportable->download($this->exportOptions['deleteAfterDownload']);
    }
}
