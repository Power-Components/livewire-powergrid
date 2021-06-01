<?php

namespace PowerComponents\LivewirePowerGrid;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class PowerGridCollection
{
    public array $columns = [];

    public static function collection(): PowerGridCollection
    {
        return new static();
    }

    public function addColumn(string $field, \Closure $closure = null): PowerGridCollection
    {
        $this->columns[$field] = $closure ?? fn ($model) => $model->{$field};

        return $this;
    }
}
