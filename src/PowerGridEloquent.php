<?php

namespace PowerComponents\LivewirePowerGrid;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class PowerGridEloquent
{
    protected $collection;

    public array $columns = [];

    public function __construct($collection)
    {
        $this->collection = $collection;
    }

    /**
     * @param $collection
     * @return PowerGridEloquent
     */
    public static function eloquent($collection = null): PowerGridEloquent
    {
        return new static($collection);
    }

    /**
     * @param string $field
     * @param \Closure|null $closure
     * @return $this
     */
    public function addColumn(string $field, \Closure $closure = null): PowerGridEloquent
    {
        $this->columns[$field] = $closure ?? fn ($model) => $model->{$field};

        return $this;
    }

    /**
     * @return array
     */
    public function make(): ?array
    {
        if (!is_a($this->collection, Collection::class)) {

            return null;
        }

        return $this->collection->map(function (Model $model) {
            // We need to generate a set of columns, which are already registered in the object, based on the model.
            // To do this we iterate through each column and then resolve the closure.
            return (object)collect($this->columns)->mapWithKeys(function ($closure, $columnName) use ($model) {
                return [$columnName => $closure($model)];
            })->toArray();
        })->toArray();
    }
}
