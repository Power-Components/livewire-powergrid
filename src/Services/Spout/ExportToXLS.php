<?php

namespace PowerComponents\LivewirePowerGrid\Services\Spout;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Box\Spout\Common\Entity\Style\{CellAlignment, Color};
use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use PowerComponents\LivewirePowerGrid\Services\Contracts\ExportInterface;
use PowerComponents\LivewirePowerGrid\Services\Export;

class ExportToXLS extends Export implements ExportInterface
{
    public function download(): BinaryFileResponse
    {
        $this->build();

        return response()
            ->download(storage_path($this->fileName . '.xlsx'));
    }

    public function store(): void
    {
        $this->build();
    }

    public function build(): void
    {
        $data = $this->prepare($this->data, $this->columns);

        $writer = WriterEntityFactory::createXLSXWriter();
        $writer->openToFile(storage_path($this->fileName . '.xlsx'));

        $style = (new StyleBuilder())
            ->setFontBold()
            ->setFontColor(Color::BLACK)
            ->setShouldWrapText(false)
            ->setCellAlignment(CellAlignment::CENTER)
            ->setBackgroundColor('d0d3d8')
            ->build();

        $row = WriterEntityFactory::createRowFromArray($data['headers'], $style);

        $writer->addRow($row);

        /** @var array<string> $row */
        foreach ($data['rows'] as $row) {
            $row = WriterEntityFactory::createRowFromArray($row);
            $writer->addRow($row);
        }

        $writer->close();
    }
}
