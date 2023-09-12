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
        x-show="open"
        x-cloak
        x-transition:enter="transform duration-200"
        x-transition:enter-start="opacity-0 scale-90"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transform duration-200"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-90"
        class="mt-2 w-auto bg-white shadow-xl absolute z-10 dark:bg-pg-primary-700"
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
