<?php


namespace PowerComponents\LivewirePowerGrid\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

trait ExportExcel
{
    public function exportToExcel(): BinaryFileResponse
    {
        $collection = $this->collection();
        $new_collection = [];
        $header = [];
        $file_name = 'excel';

        if (count($this->checkbox_values)) {
            $collection = $collection->whereIn('id', $this->checkbox_values);
        }

        foreach ($collection as $collect) {
            $item = [];
            foreach ($this->columns() as $column) {
                if ($column->hidden === false && $column->visible_in_export === true) {
                    foreach ($collect as $key => $value) {
                        if ($key === $column->field) {
                            $item[$column->title] = $value;
                        }
                    }
                    if (!in_array($column->title, $header)) {
                        $header[] = $column->title;
                    }
                }

            }
            $new_collection[] = $item;
        }
        $headers[] = $header;
        $build_xlsx = \SimpleXLSXGen::fromArray(array_merge($headers, $new_collection), $file_name);

        Storage::disk('public')->put($file_name . '_export.xlsx', $build_xlsx);

        return response()
            ->download(storage_path("app/public/" . $file_name . '_export.xlsx'));
    }
}
