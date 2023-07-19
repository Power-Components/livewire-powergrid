<?php

namespace PowerComponents\LivewirePowerGrid\Filters\Builders;

use PowerComponents\LivewirePowerGrid\Filters\FilterBase;

class BuilderBase
{
    public static function make(FilterBase $filterBase): self
    {
        return new self($filterBase);
    }

    public function __construct(
        protected null|array|FilterBase $filterBase = null
    ) {
    }
}
