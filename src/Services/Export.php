<?php

namespace PowerComponents\LivewirePowerGrid\Services;

use Exception;
use Illuminate\Support\Collection;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Helpers\Helpers;

class Export
{
    public string $fileName;

    public Collection $data;

    /** @var array<Column> $columns */
    public array $columns;

    public function fileName(string $name): Export
    {
        $this->fileName = $name;

        return $this;
    }

    /**
     * @param array<Column> $columns
     * @param Collection $data
     * @return Export
     */
    public function setData(array $columns, Collection $data): Export
    {
        $this->columns    = $columns;
        $this->data       = collect($data->toArray());

        return $this;
    }

    /**
     * @param Collection $data
     * @param array<Column> $columns
     * @throws Exception
     *
     * @return array<string, array>.
     */
    public function prepare(Collection $data, array $columns): array
    {
        $header = collect();

        $helperClass = resolve(Helpers::class);

        $data   = $data->transform(function ($row) use ($columns, $header, $helperClass) {
            $item = collect();

            collect($columns)->each(function ($column) use ($row, $header, $item, $helperClass) {
                $rules            = $helperClass->makeActionRules('pg:checkbox', $row);
                $isExportable     = false;

                if (isset($rules['hide']) || isset($rules['disable'])) {
                    $isExportable   = true;
                }

                /** @var Column $column */
                if ($column->visibleInExport || (!$column->hidden && is_null($column->visibleInExport)) && !$isExportable) {
                    foreach ($row as $key => $value) {
                        if ($key === $column->field) {
                            $item->put($column->title, $value);
                        }
                    }
                    if (!$header->contains($column->title)) {
                        $header->push($column->title);
                    }
                }
            });

            return $item->toArray();
        });

        return [
            'headers' => $header->toArray(),
            'rows'    => $data->toArray(),
        ];
    }
}
