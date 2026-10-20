<?php

namespace PowerComponents\LivewirePowerGrid;

use Closure;
use PowerComponents\Turbine\Fields;

/**
 * @see Fields
 */
class PowerGridFields extends Fields
{
    /**
     * @var array<string, true>
     */
    public array $rawFields = [];

    public function add(string $fieldName, ?Closure $closure = null): static
    {
        parent::add($fieldName, $closure);

        if ($closure === null) {
            unset($this->rawFields[$fieldName]);
        } else {
            $this->rawFields[$fieldName] = true;
        }

        return $this;
    }
}
