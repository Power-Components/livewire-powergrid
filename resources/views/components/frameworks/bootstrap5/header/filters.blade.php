<div
    wire:key="toggle-filters-{{ $tableName }}')"
    id="toggle-filters"
    class="d-flex me-2 mt-2 mt-sm-0 gap-2"
>
    <button
        wire:click="toggleFilters"
        type="button"
        class="btn btn-outline-secondary btn-sm"
    >
        <x-livewire-powergrid::icons.filter class="bi bi-filter" />
        <span>Filters</span>
    </button>
</div>
