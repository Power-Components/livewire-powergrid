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
            return $next($this->applyMultipleSort($collection));
        }

        return $next($this->applySingleSort($collection, $this->component->sortField, $this->component->sortDirection));
    }

    private function applySingleSort(Collection $collection, string $sortField, string $direction): Collection
    {
        $sortCallback = $this->component->getSortCallback($sortField);

        if ($sortCallback !== null) {
            return $sortCallback($collection, $direction);
        }

        $isDescending = $direction === 'desc';

        return $collection->sortBy($sortField, SORT_REGULAR, $isDescending);
    }

    private function applyMultipleSort(Collection $collection): Collection
    {
        $sortArray = [];
        $callbackFields = [];

        foreach ($this->component->sortArray as $sortField => $sortDirection) {
            $sortCallback = $this->component->getSortCallback($sortField);

            if ($sortCallback !== null) {
                $callbackFields[] = ['field' => $sortField, 'direction' => $sortDirection, 'callback' => $sortCallback];

                continue;
            }

            $sortArray[] = [$sortField, $sortDirection];
        }

        // Apply standard sorting first
        if (filled($sortArray)) {
            $collection = $collection->sortBy($sortArray);
        }

        // Apply callback sorting
        foreach ($callbackFields as $callbackField) {
            $collection = $callbackField['callback']($collection, $callbackField['direction']);
        }

        return $collection;
    }
}
