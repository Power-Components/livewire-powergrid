<?php

namespace PowerComponents\LivewirePowerGrid\Lite\Traits;

/** @phpstan-ignore trait.unused */
trait WithSorting
{
    public string $sortField = '';

    public string $sortDirection = 'asc';

    public bool $multiSort = false;

    public array $sortArray = [];

    private array $sortCache = [];

    public function sortBy(string $field, string $direction = 'asc'): void
    {
        if ($this->multiSort) {
            $this->sortByMultiple($field);
            $this->afterSortBy();

            return;
        }

        $this->sortDirection = $this->sortField === $field
            ? ($this->sortDirection === 'asc' ? 'desc' : 'asc')
            : $direction;

        $this->sortField = $field;
        $this->sortCache = [];

        $this->afterSortBy();
    }

    public function isSorted(string $field): bool
    {
        return $this->sortCache["sorted:{$field}"] ??= $this->multiSort
            ? array_key_exists($field, $this->sortArray)
            : $this->sortField === $field;
    }

    public function sortDirectionFor(string $field): ?string
    {
        return $this->sortCache["direction:{$field}"] ??= $this->multiSort
            ? ($this->sortArray[$field] ?? null)
            : ($this->sortField === $field ? $this->sortDirection : null);
    }

    public function sortIconFor(string $field): string
    {
        return $this->sortCache["icon:{$field}"] ??= $this->resolveSortIcon($field);
    }

    private function resolveSortIcon(string $field): string
    {
        if ($this->multiSort && array_key_exists($field, $this->sortArray)) {
            return $this->sortArray[$field] === 'desc'
                ? 'livewire-powergrid::components.icons.chevron-up'
                : 'livewire-powergrid::components.icons.chevron-down';
        }

        if ($this->multiSort || $this->sortField !== $field) {
            return 'livewire-powergrid::components.icons.chevron-up-down';
        }

        return $this->sortDirection === 'desc'
            ? 'livewire-powergrid::components.icons.chevron-up'
            : 'livewire-powergrid::components.icons.chevron-down';
    }

    private function sortByMultiple(string $field): void
    {
        if (array_key_exists($field, $this->sortArray)) {
            if ($this->sortArray[$field] === 'asc') {
                $this->sortArray[$field] = 'desc';
            } else {
                unset($this->sortArray[$field]);
            }

            $this->sortCache = [];

            return;
        }

        $this->sortArray[$field] = 'asc';
        $this->sortCache = [];
    }

    private function afterSortBy(): void
    {
        if (method_exists($this, 'persistState')) {
            $this->persistState('sorting');
        }
    }
}
