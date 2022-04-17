<?php

namespace PowerComponents\LivewirePowerGrid;

final class Exportable
{
    public const TYPE_XLS = 'excel';
    public const TYPE_CSV = 'csv';

    public string $name = 'exportable';

    public array $type = [];

    public bool $striped = false;

    public bool $deleteFileAfterSend = true;

    public function __construct(public string $fileName = 'export')
    {
    }

    public static function make(string $fileName): self
    {
        return new Exportable($fileName);
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
