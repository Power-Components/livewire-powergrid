<div x-data="{ open: false, countChecked: @entangle('checkboxValues').live }" wire:key="export-dropdown-{{ $tableName }}" class="relative">
    @if (filled(data_get($setUp, 'exportable')))
        <button
            type="button"
            @click="open = !open"
            @click.outside="open = false"
            class="{{ theme('header.layout.actions') }}"
        >
            <x-livewire-powergrid::icons.download class="h-4 w-4" />
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 opacity-60" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
        </button>

        <div
            x-show="open"
            x-transition
            class="absolute left-0 z-50 mt-1 min-w-[10rem] rounded-xl border border-zinc-200 dark:border-white/10 bg-white dark:bg-zinc-800 shadow-lg py-1"
        >
            @if (in_array('xlsx', data_get($setUp, 'exportable.type')))
                <button
                    type="button"
                    wire:click.prevent="exportToXLS"
                    @click="open = false"
                    class="w-full text-left px-3 py-2 text-sm text-zinc-700 dark:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-white/8 transition-colors"
                >
                    @lang('XLSX') -
                    @if (count($enabledFilters) === 0)
                        @lang('livewire-powergrid::datatable.labels.all')
                    @else
                        @lang('livewire-powergrid::datatable.labels.filtered')
                    @endif
                    ({{ $this->total }})
                </button>
                @if ($checkbox)
                    <button
                        type="button"
                        wire:click.prevent="exportToXLS(true)"
                        @click="open = false"
                        x-bind:disabled="countChecked.length === 0"
                        class="w-full text-left px-3 py-2 text-sm text-zinc-700 dark:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-white/8 transition-colors disabled:opacity-40 disabled:cursor-not-allowed"
                    >
                        @lang('XLSX') - @lang('livewire-powergrid::datatable.labels.selected')
                        <span x-text="`(${countChecked.length})`"></span>
                    </button>
                @endif
            @endif

            @if (in_array('csv', data_get($setUp, 'exportable.type')))
                <button
                    type="button"
                    wire:click.prevent="exportToCsv"
                    @click="open = false"
                    class="w-full text-left px-3 py-2 text-sm text-zinc-700 dark:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-white/8 transition-colors"
                >
                    @lang('CSV') -
                    @if (count($enabledFilters) === 0)
                        @lang('livewire-powergrid::datatable.labels.all')
                    @else
                        @lang('livewire-powergrid::datatable.labels.filtered')
                    @endif
                    ({{ $this->total }})
                </button>
                @if ($checkbox)
                    <button
                        type="button"
                        wire:click.prevent="exportToCsv(true)"
                        @click="open = false"
                        x-bind:disabled="countChecked.length === 0"
                        class="w-full text-left px-3 py-2 text-sm text-zinc-700 dark:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-white/8 transition-colors disabled:opacity-40 disabled:cursor-not-allowed"
                    >
                        @lang('CSV') - @lang('livewire-powergrid::datatable.labels.selected')
                        <span x-text="`(${countChecked.length})`"></span>
                    </button>
                @endif
            @endif
        </div>
    @endif
</div>
