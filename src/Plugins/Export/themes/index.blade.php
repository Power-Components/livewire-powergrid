@props([
    'tableName' => null,
    'types' => [],
    'total' => 0,
    'enabledFiltersCount' => 0,
    'checkbox' => false,
])

<div
    class="mt-2 sm:mt-0"
    id="pg-header-export"
    x-data="pgExport"
    x-on:keydown.esc="close()"
    x-on:click.outside="close()"
>
    <button
        @click.prevent="openMenu()"
        class="focus:ring-accent focus-within:focus:ring-accent focus-within:ring-accent dark:focus-within:ring-accent flex rounded-md ring-1 transition focus-within:ring-2 dark:ring-zinc-600 dark:text-zinc-300 text-zinc-600 ring-zinc-300 dark:bg-zinc-800 bg-white dark:placeholder-zinc-400 rounded-md border-0 bg-transparent py-2 px-3 ring-0 placeholder:text-zinc-400 focus:outline-none sm:text-sm sm:leading-6 w-auto"
    >
        <div class="flex">
            <x-livewire-powergrid::icons.download class="h-5 w-5 text-zinc-500 dark:text-zinc-300" />
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
        class="absolute z-10 mt-2 rounded-md dark:bg-zinc-700 bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
        tabindex="-1"
        @keydown.tab="close()"
        @keydown.enter.prevent="close()"
        @keyup.space.prevent="close()"
    >
        @if (in_array('xlsx', $types))
            <div class="flex items-center px-4 py-1 text-zinc-400 dark:text-zinc-300 border-b border-zinc-100 dark:border-zinc-600">
                <span class="w-12">@lang('XLSX')</span>
                <button
                    wire:click.prevent="exportToXLS"
                    x-on:click="close()"
                    href="#"
                    class="px-2 py-1 block text-zinc-800 hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-200 dark:hover:bg-zinc-800 rounded"
                >
                    <span class="export-count text-xs">({{ $total }})</span>
                    @if ($enabledFiltersCount === 0)
                        @lang('livewire-powergrid::datatable.labels.all')
                    @else
                        @lang('livewire-powergrid::datatable.labels.filtered')
                    @endif

                </button>
                @if ($checkbox)
                    <button wire:click.prevent="exportToXLS(true)"
                       x-on:click="close()"
                       x-bind:disabled="isEmpty()"
                       :class="disabledClass()"
                       class="px-2 py-1 block text-zinc-800 hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-200 dark:hover:bg-zinc-800 rounded"
                    >
                        <span class="export-count text-xs" x-text="countLabel()"></span> @lang('livewire-powergrid::datatable.labels.selected')
                    </button>
                @endif
            </div>
        @endif
        @if (in_array('csv', $types))
            <div class="flex items-center px-4 py-1 text-zinc-400 dark:text-zinc-300">
                <span class="w-12">@lang('Csv')</span>
                <button
                    wire:click.prevent="exportToCsv"
                    x-on:click="close()"
                    class="px-2 py-1 block text-zinc-800 hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-200 dark:hover:bg-zinc-800 rounded"
                >
                    <span class="export-count text-xs">({{ $total }})</span>
                    @if ($enabledFiltersCount === 0)
                        @lang('livewire-powergrid::datatable.labels.all')
                    @else
                        @lang('livewire-powergrid::datatable.labels.filtered')
                    @endif
                </button>
                @if ($checkbox)
                    <button
                        wire:click.prevent="exportToCsv(true)"
                        x-on:click="close()"
                        x-bind:disabled="isEmpty()"
                        :class="disabledClass()"
                        class="px-2 py-1 block text-zinc-800 hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-200 dark:hover:bg-zinc-800 rounded"
                    >
                        <span class="export-count text-xs" x-text="countLabel()"></span> @lang('livewire-powergrid::datatable.labels.selected')
                    </button>
                @endif
            </div>
        @endif
    </div>
</div>
