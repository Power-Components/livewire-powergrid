<?php

namespace PowerComponents\LivewirePowerGrid\Concerns;

use Exception;
use Illuminate\Support\Facades\{Cache, Cookie, Session};
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\Turbine\DataSource\Support\Sql;
use Psr\SimpleCache\InvalidArgumentException;

/** @codeCoverageIgnore */
trait Persist
{
    /** @var list<string> */
    public array $persist = [];

    public string $persistPrefix = '';

    /**
     * $tableItems: 'filters', 'columns', 'sorting',
     * $prefix: Add prefix to the persist storage key
     */
    /** @param  list<string>  $tableItems */
    public function persist(array $tableItems, string $prefix = ''): PowerGridComponent
    {
        $this->persist = $tableItems;
        $this->persistPrefix = $prefix;

        return $this;
    }

    /**
     * @throws Exception
     */
    public function persistState(string $tableItem): void
    {
        $persistFilterBuilder = $this->filterBuilderPersists();

        if (empty($this->persist) && ! $persistFilterBuilder) {
            return;
        }

        $state = [];

        if (in_array('columns', $this->persist) || $tableItem === 'columns') {
            $state['columns'] = collect($this->columns)
                ->map(fn ($column) => (object) $column)
                ->mapWithKeys(fn ($column) => [$column->field => $column->hidden])
                ->all();
        }

        $persistFilters = in_array('filters', $this->persist) || $tableItem === 'filters';

        if ($persistFilters) {
            $state['filters'] = $this->filters;
            $state['enabledFilters'] = $this->enabledFilters;
        }

        if (($persistFilters || $persistFilterBuilder) && ! empty($this->filterBuilder['rows'] ?? [])) {
            $state['filterBuilder'] = $this->filterBuilder;
        }

        if (in_array('sorting', $this->persist) || $tableItem === 'sorting') {
            $state['sortField'] = $this->sortField;
            $state['sortDirection'] = $this->sortDirection;
            $state['sortArray'] = $this->sortArray;
            $state['multiSort'] = $this->multiSort;
        }

        $jsonState = strval(json_encode($state));

        match ($this->getPersistDriverConfig()) {
            'session' => Session::put($this->getPersistKeyName(), $jsonState),
            'cache' => Cache::store($this->getPersistDriverStoreConfig())->put($this->getPersistKeyName(), $jsonState),
            default => Cookie::queue($this->getPersistKeyName(), $jsonState, 60 * 24 * 365 * 5) // 5 years, in minutes (Cookie::queue expects minutes)
        };
    }

    /**
     * @throws Exception|InvalidArgumentException
     */
    private function restoreState(): void
    {
        $persistFilterBuilder = $this->filterBuilderPersists();

        if (empty($this->persist) && ! $persistFilterBuilder) {
            return;
        }

        /** @var string $storage */
        $storage = match ($this->getPersistDriverConfig()) {
            'session' => Session::get($this->getPersistKeyName()),
            'cache' => Cache::store($this->getPersistDriverStoreConfig())->get($this->getPersistKeyName()),
            default => Cookie::get($this->getPersistKeyName())
        };

        $state = (array) json_decode($storage, true);

        if (in_array('columns', $this->persist) && array_key_exists('columns', $state)) {
            $this->columns = array_values(collect($this->columns)->map(function ($column) use ($state) {
                $column = (object) $column;

                /** @var string $field */
                $field = $column->field;

                if (! $column->forceHidden && array_key_exists($field, $state['columns'])) {
                    data_set($column, 'hidden', $state['columns'][$field]);
                }

                return $column;
            })->all());
        }

        if (in_array('filters', $this->persist) && array_key_exists('filters', $state)) {
            $this->filters = $state['filters'];
            $this->enabledFilters = $state['enabledFilters'];
        }

        if (($persistFilterBuilder || in_array('filters', $this->persist))
            && array_key_exists('filterBuilder', $state)
            && is_array($state['filterBuilder'])) {
            /** @var array<string, mixed> $restoredFilterBuilder */
            $restoredFilterBuilder = $state['filterBuilder'];
            $this->filterBuilder = $restoredFilterBuilder;

            if ($persistFilterBuilder && ! in_array('filters', $this->persist)) {
                $this->syncFilterBuilderPills();
            }
        }

        if (in_array('sorting', $this->persist) && array_key_exists('sortField', $state)) {
            $this->sortField = $state['sortField'];
            $this->sortDirection = Sql::sanitizeSortDirection($state['sortDirection'] ?? null);
            $this->sortArray = $state['sortArray'];
            $this->multiSort = $state['multiSort'];
        }
    }

    /**
     * @throws Exception
     */
    private function getPersistDriverConfig(): string
    {
        /** @var string $persistDriver */
        $persistDriver = config('livewire-powergrid.persist_driver', 'cookies');

        if (! in_array($persistDriver, ['session', 'cache', 'cookies'])) {
            throw new Exception('Invalid persist driver');
        }

        return $persistDriver;
    }

    private function getPersistDriverStoreConfig(): string
    {
        /** @var string $store */
        $store = config('livewire-powergrid.persist_driver_store');

        return $store;
    }

    private function getPersistKeyName(): string
    {
        if (! empty($this->persistPrefix)) {
            return 'pg:'.$this->persistPrefix.'-'.$this->tableName;
        }

        return 'pg:'.$this->tableName;
    }
}
