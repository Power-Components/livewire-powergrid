<?php

namespace PowerComponents\LivewirePowerGrid\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\{Locked, On};
use Livewire\Component;

/** @codeCoverageIgnore */
class Detail extends Component
{
    #[Locked]
    public string $tableName = '';

    #[Locked]
    public int|string $rowId = '';

    public string $trClass = '';

    public bool $show = false;

    #[Locked]
    public mixed $row = null;

    #[Locked]
    public string $view = '';

    #[Locked]
    public mixed $options = null;

    public bool $singleExpand = false;

    #[On('pg-toggle-detail-{tableName}-{rowId}')]
    public function toggle(?bool $collapsed = false): void
    {
        if (is_null($collapsed)) {
            $collapsed = ! $this->show;
        }

        $this->show = (bool) $collapsed;

        $this->dispatch('pg-toggle-detail-'.strtolower($this->tableName).'-loaded');
    }

    #[On('pg-toggle-detail-{tableName}-hidden-all')]
    public function hiddenAll(): void
    {
        $this->show = false;
    }

    public function render(): View
    {
        /** @var view-string $viewName */
        $viewName = 'livewire-powergrid::livewire.detail';

        return view($viewName);
    }
}
