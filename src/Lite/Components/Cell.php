<?php

namespace PowerComponents\LivewirePowerGrid\Lite\Components;

use Illuminate\View\Component;

/**
 * PowerGrid Lite Cell component.
 *
 * Renders a themed <td> element with alignment and variant support.
 * Usage: <x-pg-cell align="end" variant="strong">$100.00</x-pg-cell>
 */
class Cell extends Component
{
    public function __construct(
        public string $align = 'start',
        public ?string $variant = null,
        public bool $sticky = false,
    ) {}

    public function alignmentClass(): string
    {
        return match ($this->align) {
            'center' => 'text-center',
            'end' => 'text-end',
            default => 'text-left',
        };
    }

    public function variantClass(): string
    {
        return match ($this->variant) {
            'strong' => 'font-semibold',
            default => '',
        };
    }

    public function render()
    {
        /** @var view-string $viewName */
        $viewName = 'livewire-powergrid::lite.cell';

        return view($viewName);
    }
}
