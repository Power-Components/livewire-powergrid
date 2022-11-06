<?php

namespace PowerComponents\LivewirePowerGrid\Traits;

use Exception;
use Illuminate\Bus\Batch;
use Illuminate\Database\Eloquent as Eloquent;
use Illuminate\Support as Support;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\{Collection, Str};
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\Jobs\ExportJob;
use PowerComponents\LivewirePowerGrid\Services\Export;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

/**
 * @property ?Batch $exportBatch
 */
trait WithExport
{
    public bool $batchExporting = false;

    public bool $batchFinished = false;

    public string $batchId = '';

    public string $batchName = 'Powergrid batch export';

    public int $total = 0;

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
    public function runOnQueue(string $exportFileType, string $exportType): bool
    {
        $this->batchExporting = true;
        $this->batchFinished  = false;

        $queues = $this->putQueuesToBus($exportFileType, $exportType);

        $batch = Bus::batch([
            $queues->toArray(),
        ])
            ->name($this->batchName)
            ->onQueue($this->getOnQueue())
            ->onConnection($this->getQueuesConnection())
            ->then(fn (Batch $batch) => $this->onBatchThen($batch))
            ->catch(fn (Batch $batch, Throwable $e) => $this->onBatchCatch($batch, $e))
            ->finally(fn (Batch $batch) => $this->onBatchFinally($batch))
            ->dispatch();

        $this->batchId = $batch->id;

        return true;
    }

    private function putQueuesToBus(string $exportableClass, string $fileExtension): Collection
    {
        $this->exportedFiles = [];
        $queues              = collect([]);
        $countQueue          = $this->getQueuesCount();
        $perPage             = $this->total / $countQueue;
        $offset              = 0;
        $limit               = $perPage;

        for ($i = 1; $i < ($countQueue + 1); $i++) {
            $fileName = 'powergrid-' . Str::kebab(strval(data_get($this->setUp, 'exportable.fileName'))) .
                '-' . ($offset + 1) .
                '-' . $limit .
                '-' . $this->id .
                '.' . $fileExtension;

            $params = [
                'exportableClass' => $exportableClass,
                'fileName'        => $fileName,
                'offset'          => $offset,
                'limit'           => $limit,
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

    /**
     * @throws Exception
     */
    public function prepareToExport(bool $selected = false): Eloquent\Collection|Support\Collection
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

        $sortField = Support\Str::of($this->sortField)->contains('.') ? $this->sortField : $currentTable . '.' . $this->sortField;

        $results = $this->resolveModel()
            ->when($inClause, function ($query, $inClause) {
                return $query->whereIn($this->primaryKey, $inClause);
            })
            ->orderBy($sortField, $this->sortDirection)
            ->get();

        return $this->transform($results);
    }

    /**
     * @throws Throwable
     */
    public function exportToXLS(bool $selected = false): BinaryFileResponse|bool
    {
        return $this->export(Exportable::TYPE_XLS, $selected);
    }

    /**
     * @throws Throwable
     */
    public function exportToCsv(bool $selected = false): BinaryFileResponse|bool
    {
        return $this->export(Exportable::TYPE_CSV, $selected);
    }

    /**
     * @throws Exception | Throwable
     *
     */
    private function export(string $exportType, bool $selected): BinaryFileResponse|bool
    {
        $exportableClass = $this->getExportableClassFromConfig($exportType);

        if ($this->getQueuesCount() > 0 && !$selected) {
            return $this->runOnQueue($exportableClass, $exportType);
        }

        if (count($this->checkboxValues) === 0 && $selected) {
            return false;
        }

        /** @var Export $exportable */
        $exportable          = new $exportableClass();
        $currentHiddenStates = collect($this->columns)
            ->mapWithKeys(fn ($column) => [data_get($column, 'field') => data_get($column, 'hidden')]);
        $columnsWithHiddenState = array_map(function ($column) use ($currentHiddenStates) {
            $column->hidden     = $currentHiddenStates[$column->field];

            return $column;
        }, $this->columns());

        /** @var string $fileName */
        $fileName = data_get($this->setUp, 'exportable.fileName');
        $exportable
            ->fileName($fileName) /** @phpstan-ignore-next-line  */
            ->setData($columnsWithHiddenState, $this->prepareToExport($selected));

        /** @phpstan-ignore-next-line  */
        return $exportable->download($this->setUp['exportable']);
    }

    private function getExportableClassFromConfig(string $exportType): string
    {
        $defaultExportable      = strval(config('livewire-powergrid.exportable.default'));

        return strval(data_get(config('livewire-powergrid.exportable'), $defaultExportable . '.' . $exportType));
    }

    private function getQueuesCount(): int
    {
        return intval(data_get($this->setUp, 'exportable.batchExport.queues', 0));
    }

    private function getQueuesConnection(): string
    {
        return strval(data_get($this->setUp, 'exportable.batchExport.onConnection'));
    }

    private function getOnQueue(): string
    {
        return strval(data_get($this->setUp, 'exportable.batchExport.onQueue'));
    }
}
