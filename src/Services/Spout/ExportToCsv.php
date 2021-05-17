<?php

namespace PowerComponents\LivewirePowerGrid\Services\Spout;

use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use PowerComponents\LivewirePowerGrid\Services\Contracts\ExportInterface;
use PowerComponents\LivewirePowerGrid\Services\Export;

class ExportToCsv extends Export implements ExportInterface
{
    public function download()
    {
        $this->build();

        return response()
            ->download(storage_path($this->fileName . '.csv'));
    }

    public function build()
    {
        $data = $this->prepare($this->collection, $this->columns);

        $writer = WriterEntityFactory::createCSVWriter();
        $writer->openToFile(storage_path($this->fileName . '.csv'));

        $row = WriterEntityFactory::createRowFromArray($data['headers']);

        $writer->addRow($row);

        foreach ($data['rows'] as $row) {
            $row = WriterEntityFactory::createRowFromArray($row);
            $writer->addRow($row);
        }

        $writer->close();
    }
}
