<?php

namespace PowerComponents\LivewirePowerGrid\Services\Spout;

use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\{Color, Style};
use OpenSpout\Common\Exception\IOException;
use OpenSpout\Writer\Exception\WriterNotOpenedException;
use OpenSpout\Writer\XLSX\Writer;
use PowerComponents\LivewirePowerGrid\Services\Contracts\ExportInterface;
use PowerComponents\LivewirePowerGrid\Services\{Export, ExportOption};
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportToXLS extends Export implements ExportInterface
{
    /**
     * @throws \Exception
     */
    public function download(array $exportOptions = []): BinaryFileResponse
    {
        $exportOptions       = $exportOptions[0];
        $deleteFileAfterSend = $exportOptions['deleteFileAfterSend'];
        $this->striped       = $exportOptions['striped'];
        $this->build();

        return response()
            ->download(storage_path($this->fileName . '.xlsx'))
            ->deleteFileAfterSend($deleteFileAfterSend);
    }

    /**
     * @throws \Exception
     */
    public function store(): void
    {
        $this->build();
    }

    /**
     * @throws WriterNotOpenedException
     * @throws IOException
     */
    public function build(): void
    {
        $data = $this->prepare($this->data, $this->columns);

        $writer  = new Writer();
        $writer->openToFile(storage_path($this->fileName . '.xlsx'));

        $style = (new Style())
            ->setFontBold()
            ->setFontName('Arial')
            ->setFontSize(12)
            ->setFontColor(Color::BLACK)
            ->setShouldWrapText(false)
            ->setBackgroundColor('d0d3d8');

        $row = Row::fromValues($data['headers'], $style);

        $writer->addRow($row);

        $default = (new Style())
            ->setFontName('Arial')
            ->setFontSize(12);

        $gray = (new Style())
            ->setFontName('Arial')
            ->setFontSize(12)
            ->setBackgroundColor('d0d3d8');

        /** @var array<string> $row */
        foreach ($data['rows'] as $key => $row) {
            if (count($row)) {
                if ($key % 2 && $this->striped) {
                    $row = Row::fromValues($row, $gray);
                } else {
                    $row = Row::fromValues($row, $default);
                }
                $writer->addRow($row);
            }
        }
        $writer->close();
    }
}
