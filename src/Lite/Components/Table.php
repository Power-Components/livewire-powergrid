<?php

namespace PowerComponents\LivewirePowerGrid\Lite\Components;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\View\Component;

class Table extends Component
{
    public function __construct(
        public ?LengthAwarePaginator $paginate = null,
        public ?string $recordCount = 'full',
    ) {}

    public function render()
    {
        return view('livewire-powergrid::lite.table');
    }
}
