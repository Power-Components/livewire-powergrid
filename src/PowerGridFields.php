<?php

namespace PowerComponents\LivewirePowerGrid;

use Closure;
use Illuminate\Support\Traits\Macroable;

final class PowerGridFields
{
    use Macroable;

    public array $fields = [];

    /**
     * @param string $field
     * @param Closure|null $closure
     * @return $this
     */
    public function add(string $field, Closure $closure = null): PowerGridFields
    {
        $this->fields[$field] = $closure ?? fn ($model) => e(strval(data_get($model, $field)));

        return $this;
    }
}
