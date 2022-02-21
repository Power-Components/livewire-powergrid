<?php

namespace PowerComponents\LivewirePowerGrid\Services\Spout;

use Box\Spout\Common\Entity\Style\{CellAlignment, Color};
use Box\Spout\Common\Exception\{IOException, InvalidArgumentException};
use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Writer\Exception\WriterNotOpenedException;
use PowerComponents\LivewirePowerGrid\Services\Contracts\ExportInterface;
use PowerComponents\LivewirePowerGrid\Services\Export;
use Symfony\Component\HttpFoundation\{BinaryFileResponse};

class ExportToXLS extends Export implements ExportInterface
{
    /**
     * @throws IOException | WriterNotOpenedException | InvalidArgumentException
     */
    public function download(bool $deleteFileAfterSend): BinaryFileResponse
    {
        $this->build();

        return response()
            ->download(storage_path($this->fileName . '.xlsx'))
            ->deleteFileAfterSend($deleteFileAfterSend);
    }

    /**
     * @throws IOException | WriterNotOpenedException | InvalidArgumentException
     */
    public function store(): void
    {
        $this->build();
    }

    /**
     * @throws IOException | WriterNotOpenedException | InvalidArgumentException
     * @throws \Exception
     */
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
            if (count($row)) {
                $row = WriterEntityFactory::createRowFromArray($row);
                $writer->addRow($row);
            }
        }

        $writer->close();
    }
}
