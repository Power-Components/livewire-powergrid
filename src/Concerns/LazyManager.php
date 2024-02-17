<?php

namespace PowerComponents\LivewirePowerGrid\Concerns;

use Illuminate\Support\Facades\File;
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

    private function throwIncompatibilityWithLazyFeature(): void
    {
        if (!data_get($this->setUp, 'lazy')) {
            return;
        }

        $viewsPath = base_path('resources/views/vendor/livewire-powergrid');

        if (!File::isDirectory($viewsPath)) {
            return;
        }

        $files = File::allFiles($viewsPath);

        foreach ($files as $file) {
            $content = (string) file_get_contents($file);

            if (str($content)->contains('$theme->layout')) {
                throw new \Exception('Your Livewire PowerGrid views are outdated and incompatible with the "Lazy" feature. See https://livewire-powergrid.com/get-started/troubleshooting.html#theme-layout-and-view-errors');
            }
        }
    }
}
