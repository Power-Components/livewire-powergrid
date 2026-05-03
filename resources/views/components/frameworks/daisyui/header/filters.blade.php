<div
    wire:key="toggle-filters-{{ $tableName }}"
    id="toggle-filters"
    class="{{ theme('header.filters.container', 'flex mr-2 mt-2 sm:mt-0 gap-3') }}"
>
    <button
        wire:click="toggleFilters"
        type="button"
        class="{{ theme('header.filters.button', 'btn btn-ghost btn-sm border-base-300') }}"
    >
        <x-livewire-powergrid::icons.filter class="h-4 w-4" />
    </button>
</div>
