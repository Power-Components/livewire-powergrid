<?php

namespace PowerComponents\LivewirePowerGrid;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Component;
use PowerComponents\LivewirePowerGrid\Traits\WithCheckbox;

class PowerGridComponentChildren extends Component
{
    use WithCheckbox;

    public mixed $data;

    public array $theme;

    public array $setUp;

    public Collection $columns;

    public string $tableName;

    public string|int $primaryKey;

    public function render(): View
    {
        return view('livewire-powergrid::livewire.powergrid-component-children');
    }
}
