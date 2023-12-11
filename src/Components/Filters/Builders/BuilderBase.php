<?php

namespace PowerComponents\LivewirePowerGrid\Components\Filters\Builders;

use Livewire\Component;
use PowerComponents\LivewirePowerGrid\Components\Filters\FilterBase;

class BuilderBase
{
    public static function make(Component $component, FilterBase $filterBase): self
    {
        return new self($component, $filterBase);
    }

    public function __construct(
        protected Component $component,
        protected null|array|FilterBase $filterBase = null
    ) {
    }
}
