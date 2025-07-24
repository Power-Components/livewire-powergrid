<?php

namespace PowerComponents\LivewirePowerGrid\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class Detail extends Component
{
    public int|string $rowId = '';

    public string $trClass = '';

    public bool $show = false;

    public mixed $row = null;

    public string $view = '';

    public mixed $options = null;

    public bool $collapseOthers = false;

    #[On('toggle-detail-{rowId}')]
    public function toggle(?bool $collapsed = false): void
    {
        if (is_null($collapsed)) {
            $collapsed = ! $this->show;
        }

        $this->show = $collapsed;

        $this->dispatch('powergrid-detail-loaded');
    }

    public function render(): View
    {
        return view('livewire-powergrid::livewire.detail');
    }
}
