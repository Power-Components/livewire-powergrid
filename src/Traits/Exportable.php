<?php

namespace PowerComponents\LivewirePowerGrid\Traits;

use Illuminate\Bus\Batch;
use Illuminate\Support\Str;
use PowerComponents\LivewirePowerGrid\Services\Spout\{ExportToCsv, ExportToXLS};

/**
 * @property Batch exportBatch
 */
trait Exportable
{
    public bool $exportOption = false;

    public string $exportFileName = 'download';

    public array $exportType = [];

    /**
     * @param string $fileName
     * @param array|string[] $type
     * @return Exportable
     */
    public function showExportOption(string $fileName, array $type = ['excel', 'csv'])
    {
        $this->exportOption   = true;
        $this->exportFileName = $fileName;
        $this->exportType     = $type;

        return $this;
    }

    /**
     * @throws \Exception
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
     * @throws \Exception
     * @throws \Throwable
     */
    public function exportToXLS(bool $selected = false)
    {
        if ($this->queues > 0 && !$selected) {
            return $this->runOnQueue(ExportToXLS::class);
        }

        if (count($this->checkboxValues) === 0 && $selected) {
            return false;
        }

        return (new ExportToXLS())
            ->fileName($this->exportFileName)
            ->setData($this->columns(), $this->prepareToExport($selected))
            ->download();
    }

    /**
     * @throws \Throwable
     */
    public function exportToCsv(bool $selected = false)
    {
        if ($this->queues > 0 && !$selected) {
            return $this->runOnQueue(ExportToCsv::class);
        }

        if (count($this->checkboxValues) === 0 && $selected) {
            return;
        }

        return (new ExportToCsv())
            ->fileName($this->exportFileName)
            ->setData($this->columns(), $this->prepareToExport($selected))
            ->download();
    }
}
