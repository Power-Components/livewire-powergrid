<?php

namespace PowerComponents\LivewirePowerGrid\Concerns\State;

trait ResolvesBeforeSearch
{
    public function applyBeforeSearch(string $field, ?string $search): ?string
    {
        $method = 'beforeSearch'.str($field)->headline()->replace(' ', '');

        if (method_exists($this, $method)) {
            /** @var string|null $result */
            $result = $this->{$method}($search);

            return $result;
        }

        if (method_exists($this, 'beforeSearch')) {
            /** @var string|null $result */
            $result = $this->beforeSearch($field, $search);

            return $result;
        }

        return $search;
    }
}
