<?php

namespace PowerComponents\LivewirePowerGrid\Components\Exports\OpenSpout\v3;

use OpenSpout\Common\Entity\Style\{Color, Style};
use OpenSpout\Common\Exception\IOException;
use OpenSpout\Writer\Common\Creator\WriterEntityFactory;
use OpenSpout\Writer\Exception\WriterNotOpenedException;
use PowerComponents\LivewirePowerGrid\Components\Exports\Contracts\ExportInterface;
use PowerComponents\LivewirePowerGrid\Components\Exports\Export;
use PowerComponents\LivewirePowerGrid\Exportable;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/** @codeCoverageIgnore */
class ExportToXLS extends Export implements ExportInterface
{
    /**
     * @throws \Exception
     */
    public function download(Exportable|array $exportOptions): BinaryFileResponse
    {
        $deleteFileAfterSend = boolval(data_get($exportOptions, 'deleteFileAfterSend'));

        $this->build();

        return response()
            ->download(storage_path($this->fileName . '.xlsx'))
            ->deleteFileAfterSend($deleteFileAfterSend);
    }

    /**
     * @throws WriterNotOpenedException|IOException
     */
    public function build(Exportable|array $exportOptions): void
    {
        $data = $this->prepare($this->data, $this->columns);

        $writer = WriterEntityFactory::createXLSXWriter();
        $writer->openToFile(storage_path($this->fileName . '.xlsx'));

        $style = (new Style())
            ->setFontBold()
            ->setFontName('Arial')
            ->setFontSize(12)
            ->setFontColor(Color::BLACK)
            ->setShouldWrapText(false)
            ->setBackgroundColor('d0d3d8');

        $row = WriterEntityFactory::createRowFromArray($data['headers'], $style);

        $writer->addRow($row);

        /** @var array<string> $row */
        foreach ($data['rows'] as $row) {
            if (count($row)) {
                $row = WriterEntityFactory::createRowFromArray($row);
                $writer->addRow($row);
            }
        }

        $writer->close();
    }
}
