<?php

namespace PowerComponents\LivewirePowerGrid\Traits;

use App\Http\Livewire\DishesTable;
use App\Models\Dish;
use Illuminate\Bus\Batch;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PowerComponents\LivewirePowerGrid\Jobs\ExportJob;
use PowerComponents\LivewirePowerGrid\Services\Spout\ExportToCsv;
use PowerComponents\LivewirePowerGrid\Services\Spout\ExportToXLS;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * @property Batch exportBatch
 */
trait Exportable
{
    public bool $exportOption = false;

    public string $exportFileName = 'download';

    public array $exportType = [];

    public bool $exporting = false;

    public bool $exportFinished = false;

    public string $batchId = '';

    public string $extension = '';

    public int $total = 0;

    public int $queues = 0;

    public int $progress = 0;

    public array $exportedFiles = [];

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

        if ($inClause) {
            $results = $this->resolveModel()->whereIn($this->primaryKey, $inClause)->get();

            return $this->transform($results);
        }

        $results = $this->resolveModel()->get();

        return $this->transform($results);
    }

    /**
     * @throws \Exception
     * @throws \Throwable
     */
    public function exportToXLS(bool $selected = false)
    {
        $this->runOnQueue(ExportToXLS::class, $selected);

        if ($this->queues === 0) {
            return (new ExportToXLS())
                ->fileName($this->exportFileName)
                ->setData($this->columns(), $this->prepareToExport($selected))
                ->download();
        }
    }

    /**
     * @throws \Throwable
     */
    public function exportToCsv(bool $selected = false)
    {
        $this->runOnQueue(ExportToCsv::class, $selected);

        if ($this->queues === 0) {
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
            $this->progress       = $this->exportBatch->progress();
            if ($this->exportFinished) {
                $this->exporting = false;
            }
        }
    }

    public function downloadExport(string $file): BinaryFileResponse
    {
        return response()->download(storage_path($file));
    }

    /**
     * @throws \Throwable
     */
    public function runOnQueue(string $type)
    {
        if ($this->queues) {
            $this->exporting      = true;
            $this->exportFinished = false;

            $queues = $this->createQueue($type);

            $batch  = Bus::batch([
                $queues->toArray(),
            ])
                ->name('PowerGrid Batch')
                ->dispatch();

            $this->batchId = $batch->id;

            return true;
        }
    }

    public function createQueue(string $type): Collection
    {
        $queues  = collect();
        $perPage = $this->total / $this->queues;
        $page    = 0;
        $limit   = $perPage;

        switch ($type) {
            case ExportToCsv::class:
                $this->extension = 'csv';

                break;
            case ExportToXLS::class:
                $this->extension = 'xlsx';
        }

        for ($i = 1; $i < ($this->queues + 1); $i++) {
            $file = 'powergrid-' . Str::kebab($this->exportFileName) . '-' . $page . '-' . $limit . '.' . $this->extension;

            $queues->push(new ExportJob(
                get_called_class(),
                $type,
                $file,
                $this->columns(),
                $page,
                $limit,
            ));

            $this->exportedFiles[] = $file;

            $page  = $limit + 1;
            $limit = ($page - 1) + $perPage;
        }

        return $queues;
    }
}
