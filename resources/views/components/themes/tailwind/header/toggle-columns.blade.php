@if (data_get($setUp, 'header.toggleColumns'))
    <div
        x-data="pgDropdown"
        @click.outside="close()"
    >
        <button
            data-cy="toggle-columns-{{ $tableName }}"
            @click.prevent="toggle()"
            class="focus:ring-accent focus-within:focus:ring-accent focus-within:ring-accent dark:focus-within:ring-accent flex rounded-md ring-1 transition focus-within:ring-2 dark:ring-zinc-600 dark:text-zinc-300 text-zinc-600 ring-zinc-300 dark:bg-zinc-800 bg-white dark:placeholder-zinc-400 rounded-md border-0 bg-transparent py-2 px-3 ring-0 placeholder:text-zinc-400 focus:outline-none sm:text-sm sm:leading-6 w-auto"
        >
            <div class="flex">
                <x-livewire-powergrid::icons.eye-off class="w-5 h-5 text-zinc-500 dark:text-zinc-300" />
            </div>
        </button>

        <div
            x-cloak
            x-show="open"
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="transform opacity-0 scale-95"
            x-transition:enter-end="transform opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="transform opacity-100 scale-100"
            x-transition:leave-end="transform opacity-0 scale-95"
            class="toggle-columns-base group absolute z-10 mt-2 w-56 rounded-md dark:bg-zinc-700 bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
            tabindex="-1"
            @keydown.tab="close()"
            @keydown.enter.prevent="close()"
            @keyup.space.prevent="close()"
        >
            <div
                role="none"
            >
                @foreach ($this->visibleColumns as $column)
                    <div
                        wire:key="toggle-column-{{ data_get($column, 'isAction') ? 'actions' : data_get($column, 'field') }}"
                        data-cy="toggle-field-{{ data_get($column, 'isAction') ? 'actions' : data_get($column, 'field') }}"
                        x-on:click="dispatch('pg:toggleColumn-{{ $tableName }}', 'field', '{{ data_get($column, 'field') }}')"
                        @class([
                            'font-semibold bg-zinc-100 dark:bg-zinc-800 ' => data_get($column, 'hidden'),
                            'py-1' => $loop->first || $loop->last,
                            'cursor-pointer text-sm flex gap-2 justify-between block px-4 py-2 text-zinc-800 hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-200 dark:hover:bg-zinc-800'
                        ])
                    >
                        <div>
                            {!! data_get($column, 'title') !!}
                        </div>
                        @if (!data_get($column, 'hidden'))
                            <x-livewire-powergrid::icons.eye class="h-5 w-5 text-zinc-200 dark:text-zinc-300 shrink-0" />
                        @else
                            <x-livewire-powergrid::icons.eye-off class="h-5 w-5 text-zinc-500 dark:text-zinc-300 shrink-0" />
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif
