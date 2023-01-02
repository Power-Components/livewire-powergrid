<?php

namespace PowerComponents\LivewirePowerGrid\Filters;

trait WithFilterBase
{
    public string $className = '';

    public function __construct(
        public string $column,
        public ?string $field = null,
    ) {
        if (is_null($this->field)) {
            $this->field = $this->column;
        }

        $this->className = get_called_class();
    }
}
