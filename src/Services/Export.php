<?php

namespace PowerComponents\LivewirePowerGrid\Services;

use Exception;
use Illuminate\Support\Collection;

class Export
{
    public string $fileName;

    public $data;

    public array $columns;

    public function fileName(string $name): Export
    {
        $this->fileName = $name;

        return $this;
    }

    public function setData(array $columns, $data): Export
    {
        $this->columns    = $columns;
        $this->data       = collect($data->toArray());

        return $this;
    }

    /**
     * @throws Exception
     */
    public function prepare(Collection $data, array $columns): array
    {
        $header = collect();

        $data   = $data->map(function ($row) use ($columns, $header) {
            $item = collect();
            collect($columns)->each(function ($column) use ($row, $header, $item) {
                if ($column->hidden === false && $column->visibleInExport === true) {
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
            'rows'    => $data->toArray()
        ];
    }
}
