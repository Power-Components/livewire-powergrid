<?php

namespace PowerComponents\LivewirePowerGrid;

final class Exportable
{
    public const TYPE_XLS      = 'excel';
    public const TYPE_CSV      = 'csv';

    public string $name = 'exportable';

    public string $csvSeparator = ',';

    public string $csvDelimiter = '"';

    public array $type = [];

    public string $striped = '';

    public array $columnWidth = [];

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
}
