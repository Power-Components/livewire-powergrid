<?php

namespace PowerComponents\LivewirePowerGrid\Concerns;

use Livewire\Attributes\Computed;

/**
 * @property-read bool $hasLazyEnabled
 */
trait LazyManager
{
    public string $additionalCacheKey = '';

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
        return md5('pg_keys_' . json_encode(array_merge($this->getCacheKeys(), [
            'additionalCacheKey' => $this->additionalCacheKey,
            'index'              => data_get($this->setUp, 'lazy.items'),
        ])));
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
