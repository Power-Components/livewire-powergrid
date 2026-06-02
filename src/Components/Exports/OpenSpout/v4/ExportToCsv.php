<?php

namespace PowerComponents\LivewirePowerGrid\Components\Exports\OpenSpout\v4;

use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Exception\IOException;
use OpenSpout\Writer\CSV\{Options, Writer};
use OpenSpout\Writer\Exception\WriterNotOpenedException;
use PowerComponents\LivewirePowerGrid\Components\Exports\Contracts\ExportInterface;
use PowerComponents\LivewirePowerGrid\Components\Exports\Export;
use PowerComponents\LivewirePowerGrid\Components\SetUp\Exportable;
use PowerComponents\LivewirePowerGrid\Support\ExportStorage;
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
            ->download($this->temporaryFile('csv'), $this->fileName.'.csv')
            ->deleteFileAfterSend($deleteFileAfterSend);
    }

    /**
     * @throws WriterNotOpenedException|IOException
     */
    public function store(Exportable|array $exportOptions): string
    {
        $this->build($exportOptions);

        try {
            return ExportStorage::put($exportOptions, $this->fileName.'.csv', $this->temporaryFile('csv'));
        } finally {
            $this->deleteTemporaryFile('csv');
        }
    }

    /**
     * @throws WriterNotOpenedException|IOException
     */
    public function build(Exportable|array $exportOptions): void
    {
        $stripTags = boolval(data_get($exportOptions, 'stripTags', false));
        $data = $this->prepare($this->data, $this->columns, $stripTags);

        $csvSeparator = strval(data_get($exportOptions, 'csvSeparator', ','));
        $csvDelimiter = strval(data_get($exportOptions, 'csvDelimiter', '"'));

        $csvOptions = new Options();
        $csvOptions->FIELD_DELIMITER = $csvSeparator;
        $csvOptions->FIELD_ENCLOSURE = $csvDelimiter;

        $writer = new Writer($csvOptions);
        $writer->openToFile($this->temporaryFile('csv'));

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
