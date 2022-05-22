<?php

namespace PowerComponents\LivewirePowerGrid\Traits;

use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\{Collection, Str};
use PowerComponents\LivewirePowerGrid\Jobs\ExportJob;
use PowerComponents\LivewirePowerGrid\Services\Spout\{ExportToCsv, ExportToXLS};
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

trait BatchableExport
{
    public bool $batchExporting = false;

    public bool $batchFinished = false;

    public string $batchId = '';

    public string $batchName = 'Powergrid batch export';

    public string $onConnection = 'sync';

    public string $onQueue = 'default';

    public int $total = 0;

    public int $queues = 0;

    public bool $showExporting = true;

    public int $batchProgress = 0;

    public array $exportedFiles = [];

    public string $exportableJobClass = ExportJob::class;

    public bool $batchErrors = false;

    public function getExportBatchProperty(): ?Batch
    {
        if (empty($this->batchId)) {
            return null;
        }

        return Bus::findBatch($this->batchId);
    }

    public function updateExportProgress(): void
    {
        if (!is_null($this->exportBatch)) {
            $this->batchFinished = $this->exportBatch->finished();
            $this->batchProgress = $this->exportBatch->progress();
            $this->batchErrors   = $this->exportBatch->hasFailures();

            if ($this->batchFinished) {
                $this->batchExporting = false;
            }

            $this->onBatchExecuting($this->exportBatch);
        }
    }

    public function downloadExport(string $file): BinaryFileResponse
    {
        return response()->download(storage_path($file));
    }

    /**
     * @throws Throwable
     */
    public function runOnQueue(string $exportFileType): bool
    {
        $this->batchExporting = true;
        $this->batchFinished  = false;

        $queues = $this->putQueuesToBus($exportFileType);

        $batch = Bus::batch([
            $queues->toArray(),
        ])
            ->name($this->batchName)
            ->onQueue($this->onQueue)
            ->onConnection($this->onConnection)
            ->then(function (Batch $batch) {
                $this->onBatchThen($batch);
            })->catch(function (Batch $batch, Throwable $e) {
                $this->onBatchCatch($batch, $e);
            })->finally(function (Batch $batch) {
                $this->onBatchFinally($batch);
            })
            ->dispatch();

        $this->batchId = $batch->id;

        return true;
    }

    private function putQueuesToBus(string $type): Collection
    {
        $this->exportedFiles = [];
        $queues              = collect([]);
        $perPage             = $this->total / $this->queues;
        $offset              = 0;
        $limit               = $perPage;
        $fileExtension       = $this->resolveFileExtension($type);

        for ($i = 1; $i < ($this->queues + 1); $i++) {
            $fileName = 'powergrid-' . Str::kebab(strval(data_get($this->setUp, 'exportable.fileName'))) .
                '-' . ($offset + 1) .
                '-' . $limit .
                '-' . $this->id .
                '.' . $fileExtension;

            $params = [
                'type'     => $type,
                'fileName' => $fileName,
                'offset'   => $offset,
                'limit'    => $limit,
            ];

            $queues->push(new $this->exportableJobClass(
                get_called_class(),
                $this->columns(),
                $params,
            ));

            $this->exportedFiles[] = $fileName;

            $offset = $limit;
            $limit  = $offset + $perPage;
        }

        return $queues;
    }

    private function resolveFileExtension(string $class): string
    {
        return match ($class) {
            ExportToCsv::class => 'csv',
            ExportToXLS::class => 'xlsx',
            default            => '',
        };
    }

    protected function onBatchExecuting(Batch $batch): void
    {
    }

    protected function onBatchThen(Batch $batch): void
    {
    }

    protected function onBatchCatch(Batch $batch, Throwable $e): void
    {
    }

    protected function onBatchFinally(Batch $batch): void
    {
    }
}
