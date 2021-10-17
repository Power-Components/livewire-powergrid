<?php

namespace PowerComponents\LivewirePowerGrid\Traits;

use App\Http\Livewire\DishesTable;
use App\Models\Dish;
use Illuminate\Bus\Batch;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PowerComponents\LivewirePowerGrid\Jobs\ExportJob;
use PowerComponents\LivewirePowerGrid\Services\Spout\ExportToCsv;
use PowerComponents\LivewirePowerGrid\Services\Spout\ExportToXLS;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

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

    public string $batchName = 'Powergrid batch export';

    public string $onConnection = 'sync';

    public string $onQueue = 'default';

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
        $this->runOnQueue(ExportToXLS::class);

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
        $this->runOnQueue(ExportToCsv::class);

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

    public function batchThen(Batch $batch)
    {
    }

    public function batchCatch(Batch $batch, Throwable $e)
    {
    }

    public function batchFinally(Batch $batch)
    {
    }

    /**
     * @throws \Throwable
     */
    public function runOnQueue(string $type)
    {
        if ($this->queues) {
            $this->exporting      = true;
            $this->exportFinished = false;

            $queues = $this->createQueues($type);

            $batch  = Bus::batch([
                $queues->toArray(),
            ])
                ->name($this->batchName)
                ->onQueue($this->onQueue)
                ->onConnection($this->onConnection)
                ->then(function (Batch $batch) {
                    return $this->batchThen($batch);
                })->catch(function (Batch $batch, Throwable $e) {
                    return $this->batchCatch($batch, $e);
                })->finally(function (Batch $batch) {
                    return $this->batchFinally($batch);
                })
                ->dispatch();

            $this->batchId = $batch->id;

            return true;
        }
    }

    public function createQueues(string $type): Collection
    {
        $this->exportedFiles = [];
        $queues              = collect();
        $perPage             = $this->total / $this->queues;
        $offset              = 0;
        $limit               = $perPage;

        switch ($type) {
            case ExportToCsv::class:
                $this->extension = 'csv';

                break;
            case ExportToXLS::class:
                $this->extension = 'xlsx';
        }

        for ($i = 1; $i < ($this->queues + 1); $i++) {
            $fileName = 'powergrid-' . Str::kebab($this->exportFileName) . '-' . $offset . '-' . $limit . '-' . $this->id . '.' . $this->extension;

            $params = [
                'type'     => $type,
                'fileName' => $fileName,
                'offset'   => $offset,
                'limit'    => $limit,
            ];

            $queues->push(new ExportJob(
                get_called_class(),
                $this->columns(),
                $params,
            ));

            $this->exportedFiles[] = $fileName;

            $offset = $limit + 1;
            $limit  = ($offset - 1) + $perPage;
        }

        return $queues;
    }
}
