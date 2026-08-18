<?php

namespace PowerComponents\LivewirePowerGrid\Concerns\State;

trait ResolvesBeforeSearch
{
    /**
     * Resolve an optional user "before search" hook off the host instance.
     *
     * Both a field-specific ("beforeSearchName") and a global ("beforeSearch")
     * hook are resolved dynamically: the host that carries this trait (a
     * Turbine component) MAY define either, neither, or both. A headless
     * context defines none, so the raw term is returned unchanged.
     */
    public function applyBeforeSearch(string $field, ?string $search): ?string
    {
        $fieldHook = 'beforeSearch'.str($field)->headline()->replace(' ', '');
        $globalHook = 'beforeSearch';

        if (method_exists($this, $fieldHook)) {
            /** @var string|null $result */
            $result = $this->{$fieldHook}($search);

            return $result;
        }

        if (method_exists($this, $globalHook)) {
            /** @var string|null $result */
            $result = $this->{$globalHook}($field, $search);

            return $result;
        }

        return $search;
    }
}
