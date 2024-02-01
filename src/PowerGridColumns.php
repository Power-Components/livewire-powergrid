<?php

namespace PowerComponents\LivewirePowerGrid;

use Closure;
use Illuminate\Support\Traits\Macroable;

/** @deprecated - Use 'PowerGridFields' instead of PowerGridColumns */
final class PowerGridColumns
{
    use Macroable;

    public array $columns = [];

    /**
     * @param string $field
     * @param Closure|null $closure
     * @deprecated - Use 'add' instead of addColumn
     * @return $this
     */
    public function addColumn(string $field, Closure $closure = null): PowerGridColumns
    {
        $this->columns[$field] = $closure ?? fn ($model) => e(strval(data_get($model, $field)));

        return $this;
    }
}
