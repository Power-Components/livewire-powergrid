<?php

namespace PowerComponents\LivewirePowerGrid\Components\Exports\OpenSpout\v3;

use OpenSpout\Common\Exception\IOException;
use OpenSpout\Writer\Common\Creator\WriterEntityFactory;
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
        $this->build();

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

        $writer = WriterEntityFactory::createCSVWriter();
        $writer->openToFile(storage_path($this->fileName . '.csv'));

        $row = WriterEntityFactory::createRowFromArray($data['headers']);

        $writer->addRow($row);

        /** @var array<string> $row */
        foreach ($data['rows'] as $row) {
            $row = WriterEntityFactory::createRowFromArray($row);
            $writer->addRow($row);
        }

        $writer->close();
    }
}
