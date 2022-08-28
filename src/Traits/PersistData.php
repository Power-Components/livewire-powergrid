<?php

namespace PowerComponents\LivewirePowerGrid\Traits;

use PowerComponents\LivewirePowerGrid\PowerGridComponent;

trait PersistData
{
    public array $persist = [];

    private function persistState(string $tableItem): void
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

        if (!empty($this->persist)) {
            $url  = parse_url(strval(filter_input(INPUT_SERVER, 'HTTP_REFERER')));
            $path = $url && array_key_exists('path', $url) ? $url['path'] : '/';
            setcookie('pg:' . $this->tableName, strval(json_encode($state)), now()->addYear()->unix(), $path);
        }
    }

    private function restoreState(): void
    {
        if (empty($this->persist)) {
            return;
        }

        $cookie = filter_input(INPUT_COOKIE, 'pg:' . $this->tableName);
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
    }

    /**
     * filters, columns
     */
    public function persist(array $tableItems): PowerGridComponent
    {
        $this->persist = $tableItems;

        return $this;
    }
}
