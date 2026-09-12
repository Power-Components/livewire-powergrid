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

    /** @var array<string, mixed> */
    public array $filters = [];

    /** @var array<string, mixed> */
    public array $draftFilters = [];

    /**
     *
     * @var array<string, string>
     */
    public array $filterOperators = [];

    /** @var list<int|string> */
    public array $filtered = [];

    /** @var list<array<string, mixed>> */
    public array $enabledFilters = [];

    /** @var array<string, mixed> */
    public array $select = [];

    public bool $showFilters = false;

    public bool $filterPanelLoaded = false;

    public bool $inlineFiltersResolved = false;

    public bool $emitClearFiltersEvent = true;

    /**
     * Override the wire binding of a filter slot for this grid, e.g.
     * `['input_text' => ['value' => 'live.debounce.800ms']]`. Merged over
     * `config('livewire-powergrid.filter_wire')`, which is merged over the
     * defaults in Support\FilterWire.
     *
     * @return array<string, array<string, string>>
     */
    public function filterWire(): array
    {
        return [];
    }
}
