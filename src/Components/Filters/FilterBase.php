<?php

namespace PowerComponents\LivewirePowerGrid\Components\Filters;

use Livewire\Wireable;

class FilterBase implements Wireable
{
    public string $className = '';

    public ?\Closure $builder = null;

    public ?\Closure $collection = null;

    public string $component = '';

    public array $attributes = [];

    public string $baseClass = '';

    public array $filterRelation = [];

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

    public function filterRelation(string $relation, string $field): self
    {
        $this->filterRelation['relation'] = $relation;
        $this->filterRelation['field']    = $field;

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

    public function toLivewire(): array
    {
        return (array) $this;
    }

    public static function fromLivewire($value)
    {
        return $value;
    }
}
