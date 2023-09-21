<?php

namespace PowerComponents\LivewirePowerGrid;

use Closure;
use Illuminate\Support\Traits\Macroable;

final class PowerGridColumns
{
    use Macroable;

    public array $columns = [];

    /**
     * @param string $field
     * @param Closure|null $closure
     * @return $this
     */
    public function addColumn(string $field, Closure $closure = null): PowerGridColumns
    {
        $this->columns[$field] = $closure ?? fn ($model) => e(strval(data_get($model, $field)));

        return $this;
    }
}
