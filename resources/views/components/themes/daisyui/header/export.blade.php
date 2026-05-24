<div wire:key="export-dropdown-wrapper-{{ $tableName }}">
    <button
        class="{{ theme('header.layout.actions') }}"
        popovertarget="export-popover-{{ $tableName }}"
        style="anchor-name: --export-{{ $tableName }}"
    >
        <x-livewire-powergrid::icons.download class="w-4 h-4" />
    </button>
    <div
        id="export-popover-{{ $tableName }}"
        popover="auto"
        x-data="{ countChecked: @entangle('checkboxValues').live }"
        class="dropdown"
        style="position-anchor: --export-{{ $tableName }}"
    >
        <div class="flex flex-col gap-3 p-3 shadow bg-base-100 rounded-box w-max mt-2 text-sm">
            @if (in_array('xlsx', data_get($setUp, 'exportable.type')))
            <div class="flex items-center gap-2">
                    <span class="w-12 font-medium text-sm">@lang('XLSX')</span>
                    <button
                        wire:click.prevent="exportToXLS"
                        class="btn btn-sm font-normal bg-base-100 hover:bg-base-200 border-base-200"
                    >
                        <span class="export-count text-xs opacity-70">({{ $this->total }})</span>
                        @if (count($enabledFilters) === 0)
                            @lang('livewire-powergrid::datatable.labels.all')
                        @else
                            @lang('livewire-powergrid::datatable.labels.filtered')
                        @endif
                    </button>
                    @if ($checkbox)
                        <button
                            wire:click.prevent="exportToXLS(true)"
                            x-bind:disabled="countChecked.length === 0"
                            :class="{ 'cursor-not-allowed': countChecked.length === 0 }"
                            class="btn btn-sm font-normal bg-base-100 hover:bg-base-200 border-base-200"
                        >
                            <span
                                class="export-count text-xs opacity-70"
                                x-text="`(${countChecked.length})`"
                            ></span> @lang('livewire-powergrid::datatable.labels.selected')
                        </button>
                    @endif
            </div>
        @endif
        @if (in_array('csv', data_get($setUp, 'exportable.type')))
            <div class="flex items-center gap-2">
                    <span class="w-12 font-medium text-sm">@lang('Csv')</span>
                    <button
                        wire:click.prevent="exportToCsv"
                        class="btn btn-sm font-normal bg-base-100 hover:bg-base-200 border-base-200"
                    >
                        <span class="export-count text-xs opacity-70">({{ $this->total }})</span>
                        @if (count($enabledFilters) === 0)
                            @lang('livewire-powergrid::datatable.labels.all')
                        @else
                            @lang('livewire-powergrid::datatable.labels.filtered')
                        @endif
                    </button>
                    @if ($checkbox)
                        <button
                            wire:click.prevent="exportToCsv(true)"
                            x-bind:disabled="countChecked.length === 0"
                            :class="{ 'cursor-not-allowed': countChecked.length === 0 }"
                            class="btn btn-sm font-normal bg-base-100 hover:bg-base-200 border-base-200"
                        >
                            <span
                                class="export-count text-xs opacity-70"
                                x-text="`(${countChecked.length})`"
                            ></span> @lang('livewire-powergrid::datatable.labels.selected')
                        </button>
                    @endif
            </div>
            @endif
        </div>
    </div>
</div>
