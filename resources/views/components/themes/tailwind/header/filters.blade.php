<div
    wire:key="toggle-filters-{{ $tableName }}"
    id="toggle-filters"
    class="flex mt-2 sm:mt-0 gap-3"
>
    <button
        wire:click="toggleFilters"
        type="button"
        class="focus:ring-accent focus-within:focus:ring-accent focus-within:ring-accent dark:focus-within:ring-accent flex rounded-md ring-1 transition focus-within:ring-2 dark:ring-zinc-600 dark:text-zinc-300 text-zinc-600 ring-zinc-300 dark:bg-zinc-800 bg-white dark:placeholder-zinc-400 rounded-md border-0 bg-transparent py-2 px-3 ring-0 placeholder:text-zinc-400 focus:outline-none sm:text-sm sm:leading-6 w-auto"
    >
        <x-livewire-powergrid::icons.filter class="h-4 w-4 text-zinc-500 dark:text-zinc-300" />
    </button>
</div>
