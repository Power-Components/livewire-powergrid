<?php

namespace PowerComponents\LivewirePowerGrid\Components\Exports\OpenSpout\v4;

use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Exception\IOException;
use OpenSpout\Writer\CSV\Writer;
use OpenSpout\Writer\Exception\WriterNotOpenedException;
use PowerComponents\LivewirePowerGrid\Components\Exports\Contracts\ExportInterface;
use PowerComponents\LivewirePowerGrid\Components\Exports\Export;
use PowerComponents\LivewirePowerGrid\Exportable;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/** @codeCoverageIgnore */
class ExportToCsv extends Export implements ExportInterface
{
    /**
     * @throws WriterNotOpenedException|IOException
     */
    public function download(Exportable|array $exportOptions): BinaryFileResponse
    {
        $deleteFileAfterSend = boolval(data_get($exportOptions, 'deleteFileAfterSend'));
        $this->build($exportOptions);

        return response()
            ->download(storage_path($this->fileName . '.csv'))
            ->deleteFileAfterSend($deleteFileAfterSend);
    }

    /**
     * @throws WriterNotOpenedException|IOException
     */
    public function build(Exportable|array $exportOptions): void
    {
        $data = $this->prepare($this->data, $this->columns);

        $csvSeparator = strval(data_get($exportOptions, 'csvSeparator', ','));
        $csvDelimiter = strval(data_get($exportOptions, 'csvDelimiter', '"'));

        $csvOptions                  = new \OpenSpout\Writer\CSV\Options();
        $csvOptions->FIELD_DELIMITER = $csvSeparator;
        $csvOptions->FIELD_ENCLOSURE = $csvDelimiter;

        $writer = new Writer($csvOptions);
        $writer->openToFile(storage_path($this->fileName . '.csv'));

        $row = Row::fromValues($data['headers']);

        $writer->addRow($row);

        /** @var array<string> $row */
        foreach ($data['rows'] as $row) {
            $row = Row::fromValues($row);
            $writer->addRow($row);
        }

        $writer->close();
    }
}
