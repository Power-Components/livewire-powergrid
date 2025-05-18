<div
    wire:key="toggle-filters-{{ $tableName }}"
    id="toggle-filters"
    class="d-flex me-2 mt-2 mt-sm-0 gap-2"
>
    <button
        wire:click="toggleFilters"
        type="button"
        class="btn btn-light btn-sm border rounded-circle d-flex align-items-center justify-content-center p-2 shadow-sm"
        style="width: 36px; height: 36px;"
    >
        <x-livewire-powergrid::icons.filter class="bi bi-filter" />
    </button>
</div>
