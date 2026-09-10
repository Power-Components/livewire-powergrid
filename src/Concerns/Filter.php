<?php

namespace PowerComponents\LivewirePowerGrid\Concerns;

/**
 * Filter surface for PowerGridComponent. Implementation lives in Concerns\Filters\*.
 */
trait Filter
{
    use Filters\BindsFilterQueryString;
    use Filters\HandlesFilterInputs;
    use Filters\ManagesFilterPanel;
    use Filters\ManagesFilterState;
    use Filters\ResolvesFilters;

    /** @var array<string, array<string, mixed>> */
    public array $filters = [];

    /** @var array<string, array<string, mixed>> */
    public array $draftFilters = [];

    /** @var list<int|string> */
    public array $filtered = [];

    /** @var list<array<string, mixed>> */
    public array $enabledFilters = [];

    /** @var array<string, mixed> */
    public array $select = [];

    public bool $showFilters = false;

    public bool $filterPanelLoaded = false;

    /**
     * Inline filter dataSource closures are resolved a single time. The inline
     * filter row is rendered with `wire:partial.ignore`, so later interactions
     * (sort, search, pagination) reuse the already-rendered <option> list
     * instead of re-running the closure query on every request.
     */
    public bool $inlineFiltersResolved = false;

    public bool $emitClearFiltersEvent = true;
}
