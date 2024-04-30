<?php

namespace PowerComponents\LivewirePowerGrid;

use Closure;
use Illuminate\Support\Traits\Macroable;

final class PowerGridFields
{
    use Macroable;

    public array $fields = [];

    /**
     * @param string $fieldName
     * @param Closure|null $closure
     * @return $this
     */
    public function add(string $fieldName, Closure $closure = null): PowerGridFields
    {
        $this->fields[$fieldName] = $closure ?? fn ($model) => $this->valueIsString(data_get($model, $fieldName));

        return $this;
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    private function valueIsString(mixed $value): mixed
    {
        return is_string($value) ? e($value) : $value;
    }
}
