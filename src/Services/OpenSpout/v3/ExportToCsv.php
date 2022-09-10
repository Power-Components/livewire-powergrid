<?php

namespace PowerComponents\LivewirePowerGrid\Services\OpenSpout\v3;

use OpenSpout\Common\Exception\IOException;
use OpenSpout\Writer\Common\Creator\WriterEntityFactory;
use OpenSpout\Writer\Exception\WriterNotOpenedException;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\Services\Contracts\ExportInterface;
use PowerComponents\LivewirePowerGrid\Services\Export;
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
    public function build(): void
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
