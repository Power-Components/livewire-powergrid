<?php

namespace PowerComponents\LivewirePowerGrid\Concerns;

use Exception;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\Turbine\DataSource\Support\Sql;
use PowerComponents\Turbine\Support\State\StatePersister;
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

        $persister = new StatePersister();
        $jsonState = $persister->serializeState(
            persistItems: $this->persist,
            tableItem: $tableItem,
            columns: $this->columns,
            filters: $this->filters,
            enabledFilters: $this->enabledFilters,
            filterBuilder: $this->filterBuilder ?? [],
            sortField: $this->sortField,
            sortDirection: $this->sortDirection,
            sortArray: $this->sortArray,
            multiSort: $this->multiSort,
            persistFilterBuilder: $persistFilterBuilder
        );

        $key = $persister->getPersistKeyName($this->tableName, $this->persistPrefix);
        $persister->save($key, $jsonState, $this->getPersistDriverConfig(), $this->getPersistDriverStoreConfig());
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

        $persister = new StatePersister();
        $key = $persister->getPersistKeyName($this->tableName, $this->persistPrefix);
        $state = $persister->retrieve($key, $this->getPersistDriverConfig(), $this->getPersistDriverStoreConfig());

        if (is_null($state)) {
            return;
        }

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

    private function getPersistDriverStoreConfig(): ?string
    {
        /** @var string|null $store */
        $store = config('livewire-powergrid.persist_driver_store');

        return is_string($store) && $store !== '' ? $store : null;
    }

    private function getPersistKeyName(): string
    {
        if (! empty($this->persistPrefix)) {
            return 'pg:'.$this->persistPrefix.'-'.$this->tableName;
        }

        return 'pg:'.$this->tableName;
    }
}
