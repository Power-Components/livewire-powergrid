<?php

namespace PowerComponents\LivewirePowerGrid;

use Livewire\Component;

class LoadMoreChildren extends Component
{
    public $item;

    public $data;

    public $theme;

    public $radio;
    public $checkbox;
    public $setUp;
    public $radioAttribute;
    public $checkboxAttribute;
    public $columns;
    public $tableName;
    public $primaryKey;

    public function render()
    {
        return view('livewire-powergrid::livewire.load-more-children');
    }
}
