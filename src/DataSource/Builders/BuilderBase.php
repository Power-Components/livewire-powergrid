<?php

namespace PowerComponents\LivewirePowerGrid\DataSource\Builders;

use Livewire\Component;
use PowerComponents\LivewirePowerGrid\Components\Filters\FilterBase;

class BuilderBase
{
    public static function make(Component $component, FilterBase $filterBase): self
    {
        return new self($component, $filterBase);
    }

    /**
     * @param  null|array<string, mixed>|FilterBase  $filterBase
     */
    public function __construct(
        protected Component $component,
        protected null|array|FilterBase $filterBase = null
    ) {}
}
