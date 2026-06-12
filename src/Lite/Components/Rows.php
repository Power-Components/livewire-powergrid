<?php

namespace PowerComponents\LivewirePowerGrid\Lite\Components;

use Illuminate\View\Component;

/**
 * PowerGrid Lite Rows component.
 *
 * Renders a themed <tbody> wrapper.
 * Usage: <x-pg-rows>...</x-pg-rows>
 */
class Rows extends Component
{
    public function render()
    {
        return view('livewire-powergrid::lite.rows');
    }
}
