<?php

namespace PowerComponents\LivewirePowerGrid\Services;

final class ExportOption
{
    public const TYPE_XLS = 'excel';
    public const TYPE_CSV = 'csv';

    public array $type = [];

    public bool $striped = false;

    public bool $deleteFileAfterSend = true;

    public function __construct(public string $fileName = 'export')
    {
    }

    public static function make(string $fileName): self
    {
        return new ExportOption($fileName);
    }

    public function type(string ...$types): self
    {
        foreach ($types as $type) {
            $this->type[] = $type;
        }

        return $this;
    }

    public function striped(bool $striped = true): self
    {
        $this->striped = $striped;

        return $this;
    }

    public function deleteFileAfterSend(bool $deleteFileAfterSend = true): self
    {
        $this->deleteFileAfterSend = $deleteFileAfterSend;

        return $this;
    }
}
