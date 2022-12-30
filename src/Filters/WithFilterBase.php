<?php

namespace PowerComponents\LivewirePowerGrid\Filters;

trait WithFilterBase
{
    public string $className = '';

    public function __construct(
        public string $column,
        public string $field
    ) {
        $this->className = get_called_class();
    }

    public function field(string $field): self
    {
        $this->field = $field;

        return $this;
    }
}
