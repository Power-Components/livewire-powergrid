<?php

namespace PowerComponents\LivewirePowerGrid\Components\SetUp;

use PowerComponents\Turbine\Components\SetUp\Exportable as TurbineExportable;

class Exportable extends TurbineExportable
{
    /** @var array<string, mixed> */
    public array $batchExport = [];

    public string $batchName = 'PowerGrid batch export';

    public string $jobClass = '';

    public ?string $progressView = null;

    public function queues(int|string $queues): self
    {
        $batchExport = $this->batchExport;
        data_set($batchExport, 'queues', $queues);
        /** @var array<string, mixed> $batchExport */
        $this->batchExport = $batchExport;

        return $this;
    }

    public function onQueue(string $onQueue): self
    {
        $batchExport = $this->batchExport;
        data_set($batchExport, 'onQueue', $onQueue);
        /** @var array<string, mixed> $batchExport */
        $this->batchExport = $batchExport;

        return $this;
    }

    public function onConnection(string $connection): self
    {
        $batchExport = $this->batchExport;
        data_set($batchExport, 'onConnection', $connection);
        /** @var array<string, mixed> $batchExport */
        $this->batchExport = $batchExport;

        return $this;
    }

    public function batchName(string $name): self
    {
        $this->batchName = $name;

        return $this;
    }

    /** @param class-string $jobClass */
    public function jobClass(string $jobClass): self
    {
        $this->jobClass = $jobClass;

        return $this;
    }

    /**
     * Opt-in batch-export progress UI. Livewire-PowerGrid progress view receives $exportState.
     */
    public function progressView(?string $view): self
    {
        $this->progressView = $view;

        return $this;
    }
}
