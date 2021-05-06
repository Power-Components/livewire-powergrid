<?php

namespace PowerComponents\LivewirePowerGrid\Services;

use Exception;
use Illuminate\Support\Collection;

class Export
{

    public string $fileName;
    public Collection $collection;
    public array $columns;
    public array $checked_values;

    public function fileName(string $name): Export
    {
        $this->fileName = $name;
        return $this;
    }

    public function fromCollection(array $columns, Collection $collection): Export
    {
        $this->columns = $columns;
        $this->collection = $collection;
        return $this;
    }

    public function withCheckedRows($checked_values): Export
    {
        $this->checked_values = $checked_values;
        return $this;
    }

    /**
     * @throws Exception
     */
    public function prepare(Collection $collection, array $columns, array $checkedValues): array
    {

        $header = collect();

        if (count($checkedValues)) {
            $collection = $collection->whereIn('id', $checkedValues);
        }

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
