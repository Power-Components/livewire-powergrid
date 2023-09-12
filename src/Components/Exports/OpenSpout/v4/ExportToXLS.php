<?php

namespace PowerComponents\LivewirePowerGrid\Components\Exports\OpenSpout\v4;

use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\{Color, Style};
use OpenSpout\Common\Exception\IOException;
use OpenSpout\Writer\Exception\WriterNotOpenedException;
use OpenSpout\Writer\XLSX\{Options, Writer};
use PowerComponents\LivewirePowerGrid\Components\Exports\Contracts\ExportInterface;
use PowerComponents\LivewirePowerGrid\Components\Exports\{Export};
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
        $this->striped       = strval(data_get($exportOptions, 'striped'));

        /** @var array $columnWidth */
        $columnWidth       = data_get($exportOptions, 'columnWidth', []);
        $this->columnWidth = $columnWidth;

        $this->build($exportOptions);

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

        $options = new Options();
        $writer  = new Writer($options);

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

        /**
         * @var int<1, max> $column
         * @var float $width
         */
        foreach ($this->columnWidth as $column => $width) {
            $options->setColumnWidth($width, $column);
        }

        $default = (new Style())
            ->setFontName('Arial')
            ->setFontSize(12);

        $gray = (new Style())
            ->setFontName('Arial')
            ->setFontSize(12)
            ->setBackgroundColor($this->striped);

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
