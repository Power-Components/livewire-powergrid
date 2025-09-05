<?php

namespace PowerComponents\LivewirePowerGrid\DataSource\Processors\Collection\Pipelines;

use Closure;
use Illuminate\Support\Collection;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class Sorting
{
    public function __construct(protected PowerGridComponent $component) {}

    public function handle(Collection $collection, Closure $next): Collection
    {
        if (blank($this->component->sortField)) {
            return $next($collection);
        }

        if ($this->component->multiSort) {
            $sortArray = [];

            foreach ($this->component->sortArray as $sortField => $sortDirection) {
                $sortArray[] = [$sortField, $sortDirection];
            }

            return $next($collection->sortBy($sortArray));
        }

        $isDescending = $this->component->sortDirection === 'desc';

        $sorted = $collection->sortBy($this->component->sortField, SORT_REGULAR, $isDescending);

        return $next($sorted);
    }
}
