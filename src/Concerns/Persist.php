<?php

namespace PowerComponents\LivewirePowerGrid\Concerns;

use Exception;
use Illuminate\Support\Facades\{Cache, Cookie, Session};
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

/** @codeCoverageIgnore */
trait Persist
{
    public array $persist = [];

    public string $persistPrefix = '';

    /**
     * $tableItems: 'filters', 'columns', 'sorting',
     * $prefix: Add prefix to the persist storage key
     */
    public function persist(array $tableItems, string $prefix = ''): PowerGridComponent
    {
        $this->persist       = $tableItems;
        $this->persistPrefix = $prefix;

        return $this;
    }

    /**
     * @throws Exception
     */
    protected function persistState(string $tableItem): void
    {
        $state = [];

        if (in_array('columns', $this->persist) || $tableItem === 'columns') {
            $state['columns'] = collect($this->columns)
                ->map(fn ($column) => (object) $column)
                ->mapWithKeys(fn ($column) => [$column->field => $column->hidden])
                ->toArray();
        }

        if (in_array('filters', $this->persist) || $tableItem === 'filters') {
            $state['filters']        = $this->filters;
            $state['enabledFilters'] = $this->enabledFilters;
        }

        if (in_array('sorting', $this->persist) || $tableItem === 'sorting') {
            $state['sortField']     = $this->sortField;
            $state['sortDirection'] = $this->sortDirection;
            $state['sortArray']     = $this->sortArray;
            $state['multiSort']     = $this->multiSort;
        }

        if (empty($this->persist)) {
            return;
        }

        $jsonState = strval(json_encode($state));

        match ($this->getPersistDriverConfig()) {
            'session' => Session::put($this->getPersistKeyName(), $jsonState),
            'cache'   => Cache::store($this->getPersistDriverStoreConfig())->put($this->getPersistKeyName(), $jsonState),
            default   => Cookie::queue($this->getPersistKeyName(), $jsonState, now()->addYears(5)->unix())
        };
    }

    /**
     * @throws Exception
     */
    private function restoreState(): void
    {
        if (empty($this->persist)) {
            return;
        }

        $storage = match ($this->getPersistDriverConfig()) {
            'session' => Session::get($this->getPersistKeyName()),
            'cache'   => Cache::store($this->getPersistDriverStoreConfig())->get($this->getPersistKeyName()),
            default   => Cookie::get($this->getPersistKeyName())
        };

        $state = (array) json_decode(strval($storage), true);

        if (in_array('columns', $this->persist) && array_key_exists('columns', $state)) {
            $this->columns = collect($this->columns)->map(function ($column) use ($state) {
                if (!$column->forceHidden && array_key_exists($column->field, $state['columns'])) {
                    data_set($column, 'hidden', $state['columns'][$column->field]);
                }

                return (object) $column;
            })->toArray();
        }

        if (in_array('filters', $this->persist) && array_key_exists('filters', $state)) {
            $this->filters        = $state['filters'];
            $this->enabledFilters = $state['enabledFilters'];
        }

        if (in_array('sorting', $this->persist) && array_key_exists('sortField', $state)) {
            $this->sortField     = $state['sortField'];
            $this->sortDirection = $state['sortDirection'];
            $this->sortArray     = $state['sortArray'];
            $this->multiSort     = $state['multiSort'];
        }
    }

    /**
     * @throws Exception
     */
    private function getPersistDriverConfig(): string
    {
        $persistDriver = strval(config('livewire-powergrid.persist_driver', 'cookies'));

        if (!in_array($persistDriver, ['session', 'cache', 'cookies'])) {
            throw new Exception('Invalid persist driver');
        }

        return $persistDriver;
    }

    private function getPersistDriverStoreConfig(): string
    {
        return strval(config('livewire-powergrid.persist_driver_store'));
    }

    private function getPersistKeyName(): string
    {
        if (!empty($this->persistPrefix)) {
            return 'pg:' . $this->persistPrefix . '-' . $this->tableName;
        }

        return 'pg:' . $this->tableName;
    }
}
