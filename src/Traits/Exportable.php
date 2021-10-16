<?php

namespace PowerComponents\LivewirePowerGrid\Traits;

use App\Models\Dish;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;
use PowerComponents\LivewirePowerGrid\Jobs\ExportJob;
use PowerComponents\LivewirePowerGrid\Services\Spout\ExportToCsv;
use PowerComponents\LivewirePowerGrid\Services\Spout\ExportToXLS;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * @property mixed exportBatch
 */
trait Exportable
{
    public bool $exportOption = false;

    public string $exportFileName = 'download';

    public array $exportType = [];

    public bool $onQueue = false;

    public bool $exporting = false;

    public bool $exportFinished = false;

    public string $batchId = '';

    public string $extension = '';

    /**
     * @param string $fileName
     * @param array|string[] $type
     * @param bool $onQueue
     * @return Exportable
     */
    public function showExportOption(string $fileName, array $type = ['excel', 'csv'], bool $onQueue = false)
    {
        $this->exportOption   = true;
        $this->exportFileName = $fileName;
        $this->exportType     = $type;
        $this->onQueue        = $onQueue;

        return $this;
    }

    /**
     * @throws \Exception
     */
    public function prepareToExport(bool $selected = false, $datasource)
    {
        $inClause = $this->filtered;

        if ($selected && filled($this->checkboxValues)) {
            $inClause = $this->checkboxValues;
        }

        if ($this->isCollection) {
            if ($inClause) {
                $results = $this->resolveCollection($datasource)->whereIn($this->primaryKey, $inClause);

                return $this->transform($results);
            }

            return $this->transform($this->resolveCollection($datasource));
        }

        if ($inClause) {
            $results = $this->resolveModel($datasource)->whereIn($this->primaryKey, $inClause)->get();

            return $this->transform($results);
        }

        $results = $this->resolveModel($datasource)->get();

        return $this->transform($results);
    }

    /**
     * @throws \Exception
     * @throws \Throwable
     */
    public function exportToXLS(bool $selected = false)
    {
        $this->runOnQueue(ExportToXLS::class, $selected);

        if (!$this->onQueue) {
            return (new ExportToXLS())
                ->fileName($this->exportFileName)
                ->setData($this->columns(), $this->prepareToExport($selected))
                ->download();
        }
    }

    /**
     * @throws \Throwable
     * @return BinaryFileResponse
     */
    public function exportToCsv(bool $selected = false)
    {
        $this->runOnQueue(ExportToCsv::class, $selected);

        if (!$this->onQueue) {
            return (new ExportToCsv())
                ->fileName($this->exportFileName)
                ->setData($this->columns(), $this->prepareToExport($selected))
                ->download();
        }
    }

    public function getExportBatchProperty(): ?Batch
    {
        if (!$this->batchId) {
            return null;
        }

        return Bus::findBatch($this->batchId);
    }

    public function updateExportProgress()
    {
        if (!is_null($this->exportBatch)) {
            $this->exportFinished = $this->exportBatch->finished();

            if ($this->exportFinished) {
                $this->exporting = false;
            }
        }
    }

    public function downloadExport(): BinaryFileResponse
    {
        return response()->download(storage_path($this->exportFileName . '.' . $this->extension));
    }

    /**
     * @throws \Throwable
     */
    public function runOnQueue(string $class, bool $selected = false)
    {
        if ($this->onQueue) {
            $this->exporting      = true;
            $this->exportFinished = false;

            switch ($class) {
                case ExportToCsv::class: $this->extension = 'csv';
                    break;
                case ExportToXLS::class: $this->extension = 'xls';
            }

            $batch = Bus::batch([
                new ExportJob($class, $this->exportFileName, $this->columns(), 0, 0, 200),
            ])->dispatch();

            $this->batchId = $batch->id;
        }
    }
}
