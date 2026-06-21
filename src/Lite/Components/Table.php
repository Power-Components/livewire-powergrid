<?php

namespace PowerComponents\LivewirePowerGrid\Lite\Components;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\View\Component;

/**
 * @template TKey of int
 * @template TValue of mixed
 */
class Table extends Component
{
    /** @param  LengthAwarePaginator<TKey, TValue>|null  $paginate */
    public function __construct(
        public ?LengthAwarePaginator $paginate = null,
        public ?string $recordCount = 'full',
    ) {}

    public function render()
    {
        /** @var view-string $viewName */
        $viewName = 'livewire-powergrid::lite.table';

        return view($viewName);
    }
}
