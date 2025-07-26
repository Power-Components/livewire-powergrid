<?php

namespace PowerComponents\LivewirePowerGrid\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class Detail extends Component
{
    public string $tableName = '';

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

        $this->show = (bool) $collapsed;

        ds($this->show);

        $this->dispatch('powergrid-detail-loaded');
    }

    #[On('toggle-detail-hidden-all-{tableName}')]
    public function hiddenAll(): void
    {
        $this->show = false;

        ds($this->show);
    }

    public function render(): View
    {
        return view('livewire-powergrid::livewire.detail');
    }
}
