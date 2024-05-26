@if (data_get($setUp, 'header.toggleColumns'))
    <div
        x-data="{ open: false }"
        class="mr-2 mt-2 sm:mt-0"
        @click.outside="open = false"
    >
        <button
            @click.prevent="open = ! open"
            class="focus:ring-primary-600 focus-within:focus:ring-primary-600 focus-within:ring-primary-600 dark:focus-within:ring-primary-600 flex rounded-md ring-1 transition focus-within:ring-2 dark:ring-pg-primary-600 dark:text-pg-primary-300 text-gray-600 ring-gray-300 dark:bg-pg-primary-800 bg-white dark:placeholder-pg-primary-400 rounded-md border-0 bg-transparent py-2 px-3 ring-0 placeholder:text-gray-400 focus:outline-none sm:text-sm sm:leading-6 w-auto"
        >
            <div class="flex">
                <x-livewire-powergrid::icons.eye-off class="w-5 h-5 text-pg-primary-500 dark:text-pg-primary-300" />
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
            class="absolute z-10 mt-2 w-56 rounded-md dark:bg-pg-primary-700 bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
            tabindex="-1"
            @keydown.tab="open = false"
            @keydown.enter.prevent="open = false;"
            @keyup.space.prevent="open = false;"
        >
            <div
                role="none"
            >
                @foreach ($this->visibleColumns as $column)
                    <div
                        wire:click="$dispatch('pg:toggleColumn-{{ $tableName }}', { field: '{{ data_get($column, 'field') }}'})"
                        wire:key="toggle-column-{{ data_get($column, 'field') }}"
                        @class([
                            'font-semibold bg-pg-primary-100 dark:bg-pg-primary-800 ' => data_get($column, 'hidden'),
                            'py-1' => $loop->first || $loop->last,
                            'cursor-pointer text-sm flex justify-between block px-4 py-2 text-pg-primary-800 hover:bg-pg-primary-100 hover:text-black-300 dark:text-pg-primary-200 dark:hover:bg-pg-primary-800'
                        ])
                    >
                        <div>
                            {!! data_get($column, 'title') !!}
                        </div>
                        @if (!data_get($column, 'hidden'))
                            <x-livewire-powergrid::icons.eye class="text-pg-primary-200 dark:text-pg-primary-300" />
                        @else
                            <x-livewire-powergrid::icons.eye-off class="text-pg-primary-500 dark:text-pg-primary-300" />
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif
