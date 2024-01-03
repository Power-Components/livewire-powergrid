<?php

namespace PowerComponents\LivewirePowerGrid\Traits;

use Livewire\Attributes\Computed;

/**
 * @property-read bool $hasLazyEnabled
 */
trait LazyManager
{
    public function loadMore(): void
    {
        $items = data_get($this->setUp, 'lazy.items');

        data_set($this->setUp, 'lazy.items', $items + 1);
    }

    #[Computed]
    public function hasLazyEnabled(): bool
    {
        return filled(data_get($this->setUp, 'lazy'));
    }

    #[Computed]
    public function getLazyKeys(): string
    {
        return 'pg_keys_' . json_encode($this->getCacheKeys());
    }

    #[Computed]
    public function canLoadMore(): bool
    {
        $count = $this->totalCurrentPage;

        $items           = data_get($this->setUp, 'lazy.items');
        $rowsPerChildren = data_get($this->setUp, 'lazy.rowsPerChildren');

        $rendered = ($items + 1) * $rowsPerChildren;

        if ($rendered === 0) {
            return false;
        }

        return $count > $rendered;
    }
}
