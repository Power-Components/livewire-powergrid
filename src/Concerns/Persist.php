<?php

namespace PowerComponents\LivewirePowerGrid\Concerns;

use Exception;
use Illuminate\Support\Facades\{Cookie, Session};
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

/** @codeCoverageIgnore */
trait Persist
{
    public array $persist = [];

    /**
     * $tableItems: 'filters', 'columns', 'sorting'
     */
    public function persist(array $tableItems): PowerGridComponent
    {
        $this->persist = $tableItems;

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

        if ($this->getPersistDriverConfig() === 'session') {
            Session::put('pg:' . $this->tableName, strval(json_encode($state)));
        } elseif ($this->getPersistDriverConfig() === 'cookies') {
            Cookie::queue('pg:' . $this->tableName, strval(json_encode($state)), now()->addYears(5)->unix());
        }
    }

    /**
     * @throws Exception
     */
    private function restoreState(): void
    {
        if (empty($this->persist)) {
            return;
        }

        $cookieOrSession = null;

        if ($this->getPersistDriverConfig() === 'session') {
            /** @var null|string $cookieOrSession */
            $cookieOrSession = Session::get('pg:' . $this->tableName);
        } elseif ($this->getPersistDriverConfig() === 'cookies') {
            /** @var null|string $cookieOrSession */
            $cookieOrSession = Cookie::get('pg:' . $this->tableName);
        }

        if (is_null($cookieOrSession)) {
            return;
        }

        $state = (array) json_decode(strval($cookieOrSession), true);

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

        if (!in_array($persistDriver, ['session', 'cookies'])) {
            throw new Exception('Invalid persist driver');
        }

        return $persistDriver;
    }
}
