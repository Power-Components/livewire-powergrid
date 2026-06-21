<?php

namespace PowerComponents\LivewirePowerGrid\Lite\Components;

use Illuminate\View\Component;

/**
 * PowerGrid Lite Row component.
 *
 * Renders a themed <tr> element with optional checkbox cell.
 * Usage: <x-pg-row :key="$user->id" :checkbox-value="$user->id">...</x-pg-row>
 */
class Row extends Component
{
    public function __construct(
        public string|int $key = '',
        public string|int|null $checkboxValue = null,
        public ?bool $striped = null,
    ) {}

    public function render()
    {
        /** @var view-string $viewName */
        $viewName = 'livewire-powergrid::lite.row';

        return view($viewName);
    }
}
