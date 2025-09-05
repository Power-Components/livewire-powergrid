<div
    wire:key="toggle-filters-{{ $tableName }}"
    id="toggle-filters"
    class="flex mr-2 mt-2 sm:mt-0 gap-3"
>
    <button
        wire:click="toggleFilters"
        type="button"
        class="btn btn-sm btn-primary"
    >
        <x-livewire-powergrid::icons.filter class="h-4 w-4" />
    </button>
</div>
