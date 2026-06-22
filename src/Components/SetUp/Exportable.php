<?php

namespace PowerComponents\LivewirePowerGrid\Components\SetUp;

use Livewire\Wireable;

final class Exportable implements Wireable
{
    public const TYPE_XLS = 'xlsx';

    public const TYPE_CSV = 'csv';

    public string $name = 'exportable';

    public string $csvSeparator = ',';

    public string $csvDelimiter = '"';

    /** @var list<string> */
    public array $type = [];

    public string $striped = '';

    /** @var array<string, mixed> */
    public array $columnWidth = [];

    public bool $deleteFileAfterSend = true;

    /** @var array<string, mixed> */
    public array $batchExport = [];

    public bool $stripTags = false;

    /** @var array<string, mixed> */
    public array $queryOptions = [];

    public string $disk = 'local';

    public string $directory = '';

    public string $batchName = 'PowerGrid batch export';

    public string $jobClass = '';

    public ?string $progressView = null;

    public function __construct(public string $fileName = 'export') {}

    public function type(string ...$types): self
    {
        foreach ($types as $type) {
            $this->type[] = $type;
        }

        return $this;
    }

    public function csvSeparator(string $separator): self
    {
        $this->csvSeparator = $separator;

        return $this;
    }

    public function csvDelimiter(string $delimiter): self
    {
        $this->csvDelimiter = $delimiter;

        return $this;
    }

    public function striped(string $color = 'd0d3d8'): self
    {
        $this->striped = $color;

        return $this;
    }

    /** @param  array<string, mixed>  $columnWidth */
    public function columnWidth(array $columnWidth): self
    {
        $this->columnWidth = $columnWidth;

        return $this;
    }

    public function deleteFileAfterSend(bool $deleteFileAfterSend = true): self
    {
        $this->deleteFileAfterSend = $deleteFileAfterSend;

        return $this;
    }

    public function queues(string $queues): self
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

    /** @param  class-string  $jobClass */
    public function jobClass(string $jobClass): self
    {
        $this->jobClass = $jobClass;

        return $this;
    }

    /**
     * Opt-in batch-export progress UI. PowerGrid ships no progress view by
     * default; pass your own Blade view name (it receives $exportState).
     */
    public function progressView(?string $view): self
    {
        $this->progressView = $view;

        return $this;
    }

    public function disk(string $disk): self
    {
        $this->disk = $disk;

        return $this;
    }

    public function directory(string $directory): self
    {
        $this->directory = $directory;

        return $this;
    }

    /** @param  array<string, mixed>  $options */
    public function queryOptions(array $options): self
    {
        $this->queryOptions = $options;

        return $this;
    }

    /** @return array<string, mixed> */
    public function toLivewire(): array
    {
        return (array) $this;
    }

    public static function fromLivewire($value)
    {
        return $value;
    }

    public function stripTags(bool $value): self
    {
        $this->stripTags = $value;

        return $this;
    }
}
