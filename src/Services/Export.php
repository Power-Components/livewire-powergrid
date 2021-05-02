<?php


namespace PowerComponents\LivewirePowerGrid\Services;


use Exception;
use Illuminate\Support\Collection;

class Export
{

    /**
     * @throws Exception
     */
    public function prepare($collection, array $columns, array $checkedValues): array
    {
        $header = [];
        $title = [];
        $new_collection = [];

        if (count($checkedValues)) {
            $collection = $collection->whereIn('id', $checkedValues);
        }

        foreach ($collection as $collect) {
            $item = [];
            foreach ($columns as $column) {
                if ($column->hidden === false && $column->visible_in_export === true) {
                    foreach ($collect as $key => $value) {
                        if ($key === $column->field) {
                            $item[$column->title] = $value;
                        }
                    }
                    if (!in_array($column->title, $title)) {
                        $title[] = $column->title;
                    }
                }
            }
            $new_collection[] = $item;
        }
        $header[] = $title;
        return array_merge($header, $new_collection);
    }

    public string $file_name;
    public Collection $collection;
    public array $columns;
    public array $checked_values;

    public function fileName(string $name): Export
    {
        $this->file_name = $name;
        return $this;
    }

    public function fromCollection(Collection $collection): Export
    {
        $this->collection = $collection;
        return $this;
    }

    public function columns($columns): Export
    {
        $this->columns = $columns;
        return $this;
    }

    public function checkedValues($checked_values): Export
    {
        $this->checked_values = $checked_values;
        return $this;
    }


}
