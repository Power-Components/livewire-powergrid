<?php


namespace PowerComponents\LivewirePowerGrid;

use Illuminate\Support\Collection;

class PowerGrid
{

    private Collection $model;
    private array $data = [];
    private array $columns = [];

    public function __construct(Collection $model)
    {
        $this->model = $model;
    }

    /**
     * @param Collection $model
     * @return PowerGrid
     */
    public static function eloquent(Collection $model): PowerGrid
    {
        return new static($model);
    }

    /**
     * @param string $field
     * @param \Closure $closure
     * @return $this
     */
    public function addColumn(string $field, \Closure $closure): PowerGrid
    {
        $this->columns[$field] = $closure;
        return $this;
    }

    /**
     * @return array
     */
    public function make(): array
    {
        $this->model->map(function ($row, $index) {
            foreach ($this->columns as $field => $closure) {
                $this->data[$index][$field] = $closure($row);
            }
        });
        return $this->prepareData();
    }

    /**
     * @return array
     */
    private function prepareData(): array
    {
        $data = [];
        foreach ($this->data as $obj) {
            $data[$obj['id']] = (object) $obj;
        }
        return $data;
    }

}
