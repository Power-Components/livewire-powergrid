<?php

namespace PowerComponents\LivewirePowerGrid\Services;

use Exception;
use Illuminate\Support\Collection;
use PowerComponents\LivewirePowerGrid\Column;

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
    */
    public function setData(array $columns, $data): Export
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
     * @return array<string, array<int, string>>
     */
    public function prepare(Collection $data, array $columns): array
    {
        $header = collect();

        $data   = $data->map(function ($row) use ($columns, $header) {
            $item = collect();
            collect($columns)->each(function ($column) use ($row, $header, $item) {
                if (!$column->hidden && $column->visibleInExport) {
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
