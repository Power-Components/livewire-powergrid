<?php

namespace PowerComponents\LivewirePowerGrid\Filters;

class FilterBase
{
    public string $className = '';

    public ?\Closure $builder = null;

    public ?\Closure $collection = null;

    public string $component = '';

    public array $attributes = [];

    public string $baseClass = '';

    public function __construct(
        public string $column,
        public ?string $field = null,
    ) {
        if (is_null($this->field)) {
            $this->field = $this->column;
        }

        $this->className = get_called_class();
    }

    public function builder(\Closure $closure): self
    {
        $this->builder = $closure;

        return $this;
    }

    public function collection(\Closure $closure): self
    {
        $this->collection = $closure;

        return $this;
    }

    public function component(string $component, array $attributes = []): self
    {
        $this->component = $component;

        $this->attributes = $attributes;

        return $this;
    }

    public function baseClass(string $attrClass): self
    {
        $this->baseClass = $attrClass;

        return $this;
    }
}
