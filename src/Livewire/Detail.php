<?php

namespace PowerComponents\LivewirePowerGrid\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

/** @codeCoverageIgnore */
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

    #[On('pg-toggle-detail-{tableName}-{rowId}')]
    public function toggle(?bool $collapsed = false): void
    {
        if (is_null($collapsed)) {
            $collapsed = ! $this->show;
        }

        $this->show = (bool) $collapsed;

        $this->dispatch('pg-toggle-detail-'.$this->tableName.'-loaded');
    }

    #[On('pg-toggle-detail-{tableName}-hidden-all')]
    public function hiddenAll(): void
    {
        $this->show = false;
    }

    public function render(): View
    {
        return view('livewire-powergrid::livewire.detail');
    }
}
