<?php

namespace PowerComponents\LivewirePowerGrid\Filters;

class FilterBase
{
    public string $className = '';

    public ?\Closure $builder = null;

    public ?\Closure $collection = null;

    public function __construct(
        public string $column,
        public ?string $field = null,
    ) {
        if (is_null($this->field)) {
            $this->field = $this->column;
        }

        $this->className = get_called_class();
    }

    public function query(\Closure $closure): static
    {
        $this->builder = $closure;

        return $this;
    }

    public function collection(\Closure $closure): static
    {
        $this->collection = $closure;

        return $this;
    }
}
