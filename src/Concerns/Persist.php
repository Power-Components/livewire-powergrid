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
    /**
     * @var list<string>
     */
    public array $persist = ['columns', 'filters', 'sorting'];

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
     * @param  list<string>  $tableItems
     */
    public function withoutPersist(array $tableItems = []): PowerGridComponent
    {
        $this->persist = $tableItems === []
            ? []
            : array_values(array_diff($this->persist, $tableItems));

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
            filterBuilder: $this->filterBuilder,
            sortField: $this->sortField,
            sortDirection: $this->sortDirection,
            sortArray: $this->sortArray,
            multiSort: $this->multiSort,
            persistFilterBuilder: $persistFilterBuilder
        );

        $key = $this->getPersistKeyName();
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
        $key = $this->getPersistKeyName();
        $state = $persister->retrieve($key, $this->getPersistDriverConfig(), $this->getPersistDriverStoreConfig());

        if (is_null($state)) {
            return;
        }

        if (in_array('columns', $this->persist) && array_key_exists('columns', $state) && is_array($state['columns'])) {
            $columnsState = $state['columns'];
            $this->columns = array_values(collect($this->columns)->map(function ($column) use ($columnsState) {
                $column = (object) $column;

                /** @var string $field */
                $field = $column->field;

                if (! $column->forceHidden && array_key_exists($field, $columnsState)) {
                    data_set($column, 'hidden', $columnsState[$field]);
                }

                return $column;
            })->all());
        }

        if (in_array('filters', $this->persist) && isset($state['filters'], $state['enabledFilters']) && is_array($state['filters']) && is_array($state['enabledFilters'])) {
            /** @var array<string, array<string, mixed>> $filters */
            $filters = $state['filters'];
            /** @var list<array<string, mixed>> $enabledFilters */
            $enabledFilters = array_values($state['enabledFilters']);
            $this->filters = $filters;
            $this->enabledFilters = $enabledFilters;
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
            $sortField = $state['sortField'] ?? '';
            if (is_string($sortField)) {
                $this->sortField = $sortField;
            }

            $sortDirection = $state['sortDirection'] ?? null;
            $this->sortDirection = Sql::sanitizeSortDirection(is_string($sortDirection) ? $sortDirection : null);

            $sortArray = $state['sortArray'] ?? [];
            if (is_array($sortArray)) {
                /** @var array<string, string> $sortArray */
                $this->sortArray = $sortArray;
            }

            $multiSort = $state['multiSort'] ?? false;
            if (is_bool($multiSort)) {
                $this->multiSort = $multiSort;
            }
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
