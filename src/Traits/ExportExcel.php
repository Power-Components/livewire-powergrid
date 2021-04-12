<?php


namespace PowerComponents\LivewirePowerGrid\Traits;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

trait ExportExcel
{
    public function exportToExcel(): BinaryFileResponse
    {
        $data = collect($this->model());

        $except = [];
        $title = [];
        $headers = [];
        $file_name = 'excel';

        if (count($this->checkbox_values)) {
            $data = $data->whereIn('id', $this->checkbox_values);
        }

        foreach ($this->columns() as $column) {
            if ($column->hidden === false) {
                $title[] = $column->title;
                $fields[] = $column->field;
            } else {
                $except[] = $column->field;
            }
        }

        $headers[] = $title;

        $data = $data->map(fn( $item) => collect($item)->except($except)->toArray());

        $build_xlsx = \SimpleXLSXGen::fromArray(array_merge($headers, $data->toArray()), $file_name);

        Storage::disk('public')->put($file_name . '_export.xlsx', $build_xlsx);

        return response()
            ->download(storage_path("app/public/" . $file_name . '_export.xlsx'));
    }
}
