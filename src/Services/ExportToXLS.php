<?php


namespace PowerComponents\LivewirePowerGrid\Services;


use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\Services\Contracts\ExportInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportToXLS extends Export implements ExportInterface
{

    /**
     * @throws Exception
     */
    public function export(): BinaryFileResponse
    {
        $data = $this->prepare($this->collection, $this->columns, $this->checked_values);
        $build = \SimpleXLSXGen::fromArray($data, $this->file_name);

        Storage::disk('public')->put($this->file_name . '.xlsx', $build);

        return response()
            ->download(storage_path("app/public/" . $this->file_name . '.xlsx'));
    }

}
