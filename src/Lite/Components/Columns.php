<?php

namespace PowerComponents\LivewirePowerGrid\Lite\Components;

use Illuminate\View\Component;

/**
 * PowerGrid Lite Columns component.
 *
 * Renders a themed <thead> wrapper.
 * Usage: <x-pg-columns :sticky="true">...</x-pg-columns>
 */
class Columns extends Component
{
    public function __construct(
        public bool $sticky = false,
    ) {}

    public function render()
    {
        /** @var view-string $viewName */
        $viewName = 'livewire-powergrid::lite.columns';

        return view($viewName);
    }
}
