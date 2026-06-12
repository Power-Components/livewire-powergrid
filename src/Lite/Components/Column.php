<?php

namespace PowerComponents\LivewirePowerGrid\Lite\Components;

use Illuminate\View\Component;

class Column extends Component
{
    public function __construct(
        public bool $sortable = false,
        public bool $sorted = false,
        public ?string $direction = null,
        public string $align = 'start',
        public bool $sticky = false,
        public bool $checkbox = false,
        public ?string $field = null,
    ) {}

    public function alignmentClass(): string
    {
        return match ($this->align) {
            'center' => 'text-center',
            'end' => 'text-end',
            default => 'text-left',
        };
    }

    public function sortIcon(): string
    {
        if (! $this->sortable) {
            return '';
        }

        if (! $this->sorted) {
            return 'livewire-powergrid::components.icons.chevron-up-down';
        }

        return $this->direction === 'desc'
            ? 'livewire-powergrid::components.icons.chevron-up'
            : 'livewire-powergrid::components.icons.chevron-down';
    }

    public function render()
    {
        return view('livewire-powergrid::lite.column');
    }
}
