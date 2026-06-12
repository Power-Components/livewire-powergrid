<?php

namespace PowerComponents\LivewirePowerGrid\Lite\Traits;

use Illuminate\Support\Facades\{Cache, Cookie, Session};

trait WithPersist
{
    public function mountWithPersist(): void
    {
        $this->restorePersistedState();
    }

    public function persistState(string $type): void
    {
        $items = $this->getPersistItems();

        if (empty($items) || ! in_array($type, $items)) {
            return;
        }

        $state = $this->gatherPersistableState();

        $jsonState = strval(json_encode($state));
        $key = $this->getPersistStorageKey();

        match ($this->resolvePersistDriver()) {
            'session' => Session::put($key, $jsonState),
            'cache' => Cache::store($this->resolvePersistStore())->put($key, $jsonState),
            default => Cookie::queue($key, $jsonState, now()->addYears(5)->diffInMinutes()),
        };
    }

    private function getPersistItems(): array
    {
        return property_exists($this, 'persist') ? $this->persist : [];
    }

    private function getPersistPrefixValue(): string
    {
        return property_exists($this, 'persistPrefix') ? $this->persistPrefix : '';
    }

    private function gatherPersistableState(): array
    {
        $state = [];
        $items = $this->getPersistItems();

        if (in_array('sorting', $items)) {
            $state['sortField'] = $this->sortField ?? '';
            $state['sortDirection'] = $this->sortDirection ?? 'asc';
            $state['sortArray'] = $this->sortArray ?? [];
            $state['multiSort'] = $this->multiSort ?? false;
        }

        if (in_array('checkbox', $items)) {
            $state['checkboxValues'] = $this->checkboxValues ?? [];
            $state['checkboxAll'] = $this->checkboxAll ?? false;
        }

        if (in_array('perPage', $items)) {
            $state['perPage'] = $this->perPage ?? 10;
        }

        return $state;
    }

    private function restorePersistedState(): void
    {
        if (empty($this->getPersistItems())) {
            return;
        }

        $key = $this->getPersistStorageKey();

        $storage = match ($this->resolvePersistDriver()) {
            'session' => Session::get($key),
            'cache' => Cache::store($this->resolvePersistStore())->get($key),
            default => Cookie::get($key),
        };

        if (empty($storage)) {
            return;
        }

        $state = (array) json_decode(strval($storage), true);

        foreach ($state as $property => $value) {
            if (property_exists($this, $property)) {
                $this->{$property} = $value;
            }
        }
    }

    private function getPersistStorageKey(): string
    {
        $prefix = $this->getPersistPrefixValue() ?: class_basename(static::class);

        return "pg_lite:{$prefix}";
    }

    private function resolvePersistDriver(): string
    {
        $driver = strval(config('livewire-powergrid.persist_driver', 'cookies'));

        if (! in_array($driver, ['session', 'cache', 'cookies'])) {
            throw new \InvalidArgumentException("Invalid PowerGrid persist driver: [{$driver}]");
        }

        return $driver;
    }

    private function resolvePersistStore(): string
    {
        return strval(config('livewire-powergrid.persist_driver_store', ''));
    }
}
