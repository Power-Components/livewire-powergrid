<?php

namespace PowerComponents\LivewirePowerGrid\Concerns;

use Illuminate\Support\Facades\Cookie;
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

        Cookie::queue('pg:' . $this->tableName, strval(json_encode($state)), now()->addYears(5)->unix());
    }

    private function restoreState(): void
    {
        if (empty($this->persist)) {
            return;
        }

        /** @var null|string $cookie */
        $cookie = Cookie::get('pg:' . $this->tableName);

        if (is_null($cookie)) {
            return;
        }

        $state = (array) json_decode(strval($cookie), true);

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
}
