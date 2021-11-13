<?php

namespace PowerComponents\LivewirePowerGrid;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection as BaseCollection;

class PowerGridEloquent
{
    protected ?BaseCollection $collection;

    public array $columns = [];

    /**
     * @param BaseCollection|null $collection
     */
    public function __construct($collection)
    {
        $this->collection = $collection;
    }

    /**
     * @param BaseCollection|null $collection
     * @return PowerGridEloquent
     */
    public static function eloquent(?BaseCollection $collection): PowerGridEloquent
    {
        /** @phpstan-ignore-next-line */
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
     * @return array|null
     */
    public function make(): ?array
    {
        if (!is_a($this->collection, BaseCollection::class)) {
            return null;
        }

        return $this->collection->map(function (Model $model) {
            // We need to generate a set of columns, which are already registered in the object, based on the model.
            // To do this we iterate through each column and then resolve the closure.
            return (object) collect($this->columns)->mapWithKeys(function ($closure, $columnName) use ($model) {
                return [$columnName => $closure($model)];
            })->toArray();
        })->toArray();
    }
}
