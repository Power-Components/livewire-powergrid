<?php

namespace PowerComponents\LivewirePowerGrid\Traits;

use Illuminate\Bus\Batch;
use Illuminate\Database\Eloquent\Collection;
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
     */
    public function prepareToExport(bool $selected = false): Collection
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

        if ($inClause) {
            $results = $this->resolveModel()->whereIn($this->primaryKey, $inClause)->get();

            return $this->transform($results);
        }

        $results = $this->resolveModel()->get();

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

        return (new ExportToXLS())
            ->fileName($this->exportFileName)
            ->setData($this->columns(), $this->prepareToExport($selected))
            ->download();
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

        return (new ExportToCsv())
            ->fileName($this->exportFileName)
            ->setData($this->columns(), $this->prepareToExport($selected))
            ->download();
    }
}
