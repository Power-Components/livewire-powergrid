<?php

namespace PowerComponents\LivewirePowerGrid\Services\Spout;

use Box\Spout\Common\Exception\{IOException, InvalidArgumentException};
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Writer\Exception\WriterNotOpenedException;
use Exception;
use PowerComponents\LivewirePowerGrid\Services\Contracts\ExportInterface;
use PowerComponents\LivewirePowerGrid\Services\Export;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportToCsv extends Export implements ExportInterface
{
    /**
     * @throws IOException | WriterNotOpenedException | InvalidArgumentException
     * @throws Exception
     */
    public function download(bool $deleteFileAfterSend): BinaryFileResponse
    {
        $this->build();

        return response()
            ->download(storage_path($this->fileName . '.csv'))
            ->deleteFileAfterSend($deleteFileAfterSend);
    }

    /**
     * @throws IOException | WriterNotOpenedException | InvalidArgumentException
     * @throws Exception
     */
    public function store(): void
    {
        $this->build();
    }

    /**
     * @throws IOException | WriterNotOpenedException | InvalidArgumentException
     * @throws Exception
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
