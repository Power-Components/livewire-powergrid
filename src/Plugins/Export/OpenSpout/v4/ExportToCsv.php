<?php

namespace PowerComponents\LivewirePowerGrid\Plugins\Export\OpenSpout\v4;

use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Exception\IOException;
use OpenSpout\Writer\CSV\{Options, Writer};
use OpenSpout\Writer\Exception\WriterNotOpenedException;
use PowerComponents\LivewirePowerGrid\Components\SetUp\Exportable;
use PowerComponents\LivewirePowerGrid\Plugins\Export\Contracts\ExportInterface;
use PowerComponents\LivewirePowerGrid\Plugins\Export\Export;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/** @codeCoverageIgnore */
/** @deprecated */
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
            ->download(storage_path($this->fileName.'.csv'))
            ->deleteFileAfterSend($deleteFileAfterSend);
    }

    /**
     * @throws WriterNotOpenedException|IOException
     */
    public function store(Exportable|array $exportOptions): void
    {
        $this->build($exportOptions);

        $disk = strval(data_get($exportOptions, 'disk', 'local'));
        $directory = strval(data_get($exportOptions, 'directory', ''));

        $filePath = storage_path($this->fileName.'.csv');

        Storage::disk($disk)
            ->putFileAs($directory, new File($filePath), $this->fileName.'.csv');

        @unlink($filePath);
    }

    /**
     * @throws WriterNotOpenedException|IOException
     */
    public function build(Exportable|array $exportOptions): void
    {
        $stripTags = boolval(data_get($exportOptions, 'stripTags', false));

        $csvSeparator = strval(data_get($exportOptions, 'csvSeparator', ','));
        $csvDelimiter = strval(data_get($exportOptions, 'csvDelimiter', '"'));

        $csvOptions = new Options();
        $csvOptions->FIELD_DELIMITER = $csvSeparator;
        $csvOptions->FIELD_ENCLOSURE = $csvDelimiter;

        $writer = new Writer($csvOptions);
        $writer->openToFile(storage_path($this->fileName.'.csv'));

        $writer->addRow(Row::fromValues($this->exportHeaders($this->columns)));

        // Stream rows one at a time so large exports never materialize in memory.
        foreach ($this->streamRows($this->data, $this->columns, $stripTags) as $row) {
            $writer->addRow(Row::fromValues($row));
        }

        $writer->close();
    }
}
