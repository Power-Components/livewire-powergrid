<div class="dropdown" :class="{ 'dropdown-open': open }" x-data="{ open: false, countChecked: @entangle('checkboxValues').live }" x-on:keydown.esc="open = false" x-on:click.outside="open = false" wire:key="export-dropdown-{{ $tableName }}">
    <div tabindex="0" role="button" class="{{ theme('header.layout.actions') }}" x-on:click="open = !open">
        <x-livewire-powergrid::icons.download class="w-4 h-4" />
    </div>
    <ul tabindex="0" class="dropdown-content z-[50] menu p-2 shadow bg-base-100 rounded-box w-max mt-2 flex flex-col gap-2">
        @if (in_array('xlsx', data_get($setUp, 'exportable.type')))
            <li class="p-0 flex-row items-center hover:bg-transparent focus:bg-transparent active:bg-transparent">
                <div class="flex items-center gap-2 p-0 hover:bg-transparent active:bg-transparent focus:bg-transparent">
                    <span class="w-12 font-medium text-sm">@lang('XLSX')</span>
                    <button wire:click.prevent="exportToXLS" x-on:click="open = false" class="btn btn-sm font-normal bg-base-100 hover:bg-base-200 border-base-200">
                        <span class="export-count text-xs opacity-70">({{ $this->total }})</span>
                        @if (count($enabledFilters) === 0)
                            @lang('livewire-powergrid::datatable.labels.all')
                        @else
                            @lang('livewire-powergrid::datatable.labels.filtered')
                        @endif
                    </button>
                    @if ($checkbox)
                        <button wire:click.prevent="exportToXLS(true)" x-on:click="open = false" x-bind:disabled="countChecked.length === 0" :class="{ 'cursor-not-allowed': countChecked.length === 0 }" class="btn btn-sm font-normal bg-base-100 hover:bg-base-200 border-base-200">
                            <span class="export-count text-xs opacity-70" x-text="`(${countChecked.length})`"></span> @lang('livewire-powergrid::datatable.labels.selected')
                        </button>
                    @endif
                </div>
            </li>
        @endif
        @if (in_array('csv', data_get($setUp, 'exportable.type')))
            <li class="p-0 flex-row items-center hover:bg-transparent focus:bg-transparent active:bg-transparent">
                <div class="flex items-center gap-2 p-0 hover:bg-transparent active:bg-transparent focus:bg-transparent">
                    <span class="w-12 font-medium text-sm">@lang('Csv')</span>
                    <button wire:click.prevent="exportToCsv" x-on:click="open = false" class="btn btn-sm font-normal bg-base-100 hover:bg-base-200 border-base-200">
                        <span class="export-count text-xs opacity-70">({{ $this->total }})</span>
                        @if (count($enabledFilters) === 0)
                            @lang('livewire-powergrid::datatable.labels.all')
                        @else
                            @lang('livewire-powergrid::datatable.labels.filtered')
                        @endif
                    </button>
                    @if ($checkbox)
                        <button wire:click.prevent="exportToCsv(true)" x-on:click="open = false" x-bind:disabled="countChecked.length === 0" :class="{ 'cursor-not-allowed': countChecked.length === 0 }" class="btn btn-sm font-normal bg-base-100 hover:bg-base-200 border-base-200">
                            <span class="export-count text-xs opacity-70" x-text="`(${countChecked.length})`"></span> @lang('livewire-powergrid::datatable.labels.selected')
                        </button>
                    @endif
                </div>
            </li>
        @endif
    </ul>
</div>
