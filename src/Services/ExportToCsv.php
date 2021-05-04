<?php


namespace PowerComponents\LivewirePowerGrid\Services;


use Illuminate\Support\Facades\Storage;
use PowerComponents\LivewirePowerGrid\Services\Contracts\ExportInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportToCsv extends Export implements ExportInterface
{

    public function download(): BinaryFileResponse
    {

        Storage::disk('public')->put($this->fileName . '.csv', $this->build());

        return response()
            ->download(storage_path("app/public/" . $this->fileName . '.csv'));

    }

    public function build()
    {
        $data = $this->prepare($this->collection, $this->columns, $this->checked_values);

        $f = fopen('php://memory', 'w');
        foreach ($data as $line) {
            fputcsv($f, $line, ";");
        }
        return $f;
    }
}
