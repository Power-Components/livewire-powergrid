<?php


namespace PowerComponents\LivewirePowerGrid;

use Illuminate\Database\Eloquent\Collection;

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
     * @param \Illuminate\Database\Eloquent\Collection $model
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
        return $this->model->map(function ($model) {
            $attributes = collect($model->getAttributes())
                        ->filter(fn ($value, $name) => array_key_exists($name, $this->columns));
            
            return (object) $attributes->map(fn ($attribute, $name) => $this->columns[$name]($model))->toArray();
        })->toArray();
    }
}
