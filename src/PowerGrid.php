<?php


namespace PowerComponents\LivewirePowerGrid;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class PowerGrid
{
    protected Collection $collection;

    protected array $columns = [];

    public function __construct(Collection $collection)
    {
        $this->collection = $collection;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Collection $collection
     * @return \PowerComponents\LivewirePowerGrid\PowerGrid
     */
    public static function eloquent(Collection $collection): PowerGrid
    {
        return new static($collection);
    }

    /**
     * @param string $field
     * @param \Closure|null $closure
     * @return $this
     */
    public function addColumn(string $field, \Closure $closure = null): PowerGrid
    {
        $this->columns[$field] = $closure ?? fn ($model) => $model->{$field};
        return $this;
    }

    /**
     * @return array
     */
    public function make(): array
    {
        return $this->collection->map(function (Model $model) {
            // We need to generate a set of columns, which are already registered in the object, based on the model.
            // To do this we iterate through each column and then resolve the closure.
            return (object) collect($this->columns)->mapWithKeys(function ($closure, $columnName) use ($model) {
                return [$columnName => $closure($model)];
            })->toArray();
        })->toArray();
    }
}
