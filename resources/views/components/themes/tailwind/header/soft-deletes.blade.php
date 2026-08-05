@if (data_get($setUp, 'header.softDeletes'))
    <div
        x-data="pgDropdown"
        @click.outside="close()"
    >
        <button
            @click.prevent="toggle()"
            class="focus:ring-accent focus-within:focus:ring-accent focus-within:ring-accent dark:focus-within:ring-accent flex rounded-md ring-1 transition focus-within:ring-2 dark:ring-zinc-600 dark:text-zinc-300 text-zinc-600 ring-zinc-300 dark:bg-zinc-800 bg-white dark:placeholder-zinc-400 rounded-md border-0 bg-transparent py-2 px-3 ring-0 placeholder:text-zinc-400 focus:outline-none sm:text-sm sm:leading-6 w-auto"
        >
            <div class="flex">
                <x-livewire-powergrid::icons.trash class="text-zinc-500 dark:text-zinc-300" />
            </div>
        </button>

        <div
            x-show="open"
            x-cloak
            x-transition:enter="transform duration-200"
            x-transition:enter-start="opacity-0 scale-90"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transform duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-90"
            class="mt-2 py-2 w-48 bg-white shadow-xl absolute z-10 dark:bg-zinc-700"
        >

            <div
                x-on:click="dispatchClose('pg:softDeletes-{{ $tableName }}', 'softDeletes', '')"
                class="cursor-pointer flex justify-start block px-4 py-2 text-zinc-800 hover:bg-zinc-50 hover:text-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-900 dark:hover:bg-zinc-700"
            >
                @lang('livewire-powergrid::datatable.soft_deletes.without_trashed')
            </div>
            <div
                x-on:click="dispatchClose('pg:softDeletes-{{ $tableName }}', 'softDeletes', 'withTrashed')"
                class="cursor-pointer flex justify-start block px-4 py-2 text-zinc-800 hover:bg-zinc-50 hover:text-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-900 dark:hover:bg-zinc-700"
            >
                @lang('livewire-powergrid::datatable.soft_deletes.with_trashed')
            </div>
            <div
                x-on:click="dispatchClose('pg:softDeletes-{{ $tableName }}', 'softDeletes', 'onlyTrashed')"
                class="cursor-pointer flex justify-start block px-4 py-2 text-zinc-800 hover:bg-zinc-50 hover:text-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-900 dark:hover:bg-zinc-700"
            >
                @lang('livewire-powergrid::datatable.soft_deletes.only_trashed')
            </div>

        </div>
    </div>
@endif
