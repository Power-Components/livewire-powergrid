<div
    x-data="{ open: false }"
    @click.outside="open = false"
>
    <button
        @click.prevent="open = ! open"
        class="pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700
    dark:ring-offset-pg-primary-800 dark:text-pg-primary-400 dark:bg-pg-primary-700"
    >
        <div class="flex">
            <x-livewire-powergrid::icons.download class="h-5 w-5 text-pg-primary-500 dark:text-pg-primary-300" />
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
        class="absolute z-10 mt-2 rounded-md dark:bg-pg-primary-700 bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
        tabindex="-1"
        @keydown.tab="open = false"
        @keydown.enter.prevent="open = false;"
        @keyup.space.prevent="open = false;"
    >
        @if (in_array('xlsx', data_get($setUp, 'exportable.type')))
            <div class="flex px-4 py-2 text-pg-primary-400 dark:text-pg-primary-300">
                <span class="w-12">@lang('XLSX')</span>
                <a
                    wire:click.prevent="exportToXLS"
                    x-on:click="open = false"
                    href="#"
                    class="px-2 block text-pg-primary-800 hover:bg-pg-primary-50 hover:text-black-300 dark:text-pg-primary-200 dark:hover:bg-pg-primary-700 rounded"
                >
                    @if (count($enabledFilters) === 0)
                        @lang('livewire-powergrid::datatable.labels.all')
                    @else
                        @lang('livewire-powergrid::datatable.labels.filtered')
                    @endif

                </a>
                @if ($checkbox)
                    <a
                        wire:click.prevent="exportToXLS(true)"
                        x-on:click="open = false"
                        href="#"
                        class="px-2 block text-pg-primary-800 hover:bg-pg-primary-50 hover:text-black-300 dark:text-pg-primary-200 dark:hover:bg-pg-primary-700 rounded"
                    >
                        @lang('livewire-powergrid::datatable.labels.selected')
                    </a>
                @endif
            </div>
        @endif
        @if (in_array('csv', data_get($setUp, 'exportable.type')))
            <div class="flex px-4 py-2 text-pg-primary-400 dark:text-pg-primary-300">
                <span class="w-12">@lang('Csv')</span>
                <a
                    wire:click.prevent="exportToCsv"
                    x-on:click="open = false"
                    href="#"
                    class="px-2 block text-pg-primary-800 hover:bg-pg-primary-50 hover:text-black-300 dark:text-pg-primary-200 dark:hover:bg-pg-primary-700 rounded"
                >
                    @if (count($enabledFilters) === 0)
                        @lang('livewire-powergrid::datatable.labels.all')
                    @else
                        @lang('livewire-powergrid::datatable.labels.filtered')
                    @endif
                </a>
                @if ($checkbox)
                    <a
                        wire:click.prevent="exportToCsv(true)"
                        x-on:click="open = false"
                        href="#"
                        class="px-2 block text-pg-primary-800 hover:bg-pg-primary-50 hover:text-black-300 dark:text-pg-primary-200 dark:hover:bg-pg-primary-700 rounded"
                    >
                        @lang('livewire-powergrid::datatable.labels.selected')
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>
