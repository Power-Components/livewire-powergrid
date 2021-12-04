<?php

namespace PowerComponents\LivewirePowerGrid\Traits;

use Illuminate\Bus\Batch;

use Illuminate\Database\Eloquent\{Collection, Model};
use Illuminate\Support\{Collection as BaseCollection, Str};
use PowerComponents\LivewirePowerGrid\Services\Spout\{ExportToCsv, ExportToXLS};
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * @property ?Batch $exportBatch
 */
trait Exportable
{
    public bool $exportOption = false;

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

        if ($inClause) {
            $results = $this->resolveModel()
                ->orderBy($sortField, $this->sortDirection)
                ->whereIn($this->primaryKey, $inClause)
                ->get();

            return $this->transform($results);
        }

        $results = $this->resolveModel()
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
        if ($this->queues > 0 && !$selected) {
            return $this->runOnQueue(ExportToXLS::class);
        }

        if (count($this->checkboxValues) === 0 && $selected) {
            return false;
        }

        $exportable = new ExportToXLS();

        $exportable
            ->fileName($this->exportFileName)
            ->setData($this->columns(), $this->prepareToExport($selected));

        return $exportable->download();
    }

    /**
     * @throws \Exception | \Throwable
     * @return BinaryFileResponse | bool
     */
    public function exportToCsv(bool $selected = false)
    {
        if ($this->queues > 0 && !$selected) {
            return $this->runOnQueue(ExportToCsv::class);
        }

        if (count($this->checkboxValues) === 0 && $selected) {
            return false;
        }

        $exportable = new ExportToCsv();

        $exportable
            ->fileName($this->exportFileName)
            ->setData($this->columns(), $this->prepareToExport($selected));

        return $exportable->download();
    }
}
