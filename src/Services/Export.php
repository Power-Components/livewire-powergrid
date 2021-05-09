<?php

namespace PowerComponents\LivewirePowerGrid\Services;

use Exception;
use Illuminate\Support\Collection;

class Export
{

    public string $fileName;
    public $collection;
    public array $columns;

    public function fileName(string $name): Export
    {
        $this->fileName = $name;
        return $this;
    }

    public function fromCollection(array $columns, $collection): Export
    {
        $this->columns = $columns;
        $this->collection = collect($collection->toArray());
        return $this;
    }

    /**
     * @throws Exception
     */
    public function prepare(Collection $collection, array $columns): array
    {

        $header = collect();

        $collection = $collection->map(function ($row) use ($columns, $header) {
            $item = collect();
            collect($columns)->each(function ($column) use ($row, $header, $item) {
                if ($column->hidden === false && $column->visible_in_export === true) {
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
            'rows' => $collection->toArray()
        ];
    }

}
