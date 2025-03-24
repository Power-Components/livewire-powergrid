<div
    x-data="{ open: false, countChecked: @entangle('checkboxValues').live }"
    class="dropdown"
>
    <div
        tabindex="0"
        role="button"
        class="btn btn-sm btn-primary"
    >
        <x-livewire-powergrid::icons.download class="h-5 w-5" />
    </div>
    <div
        tabindex="0"
        class="dropdown-content card card-sm bg-base-100 z-1 shadow-md"
    >
        <div class="card-body">
            @if (in_array('xlsx', data_get($setUp, 'exportable.type')))
                <div class="flex items-center gap-2">
                    <span class="w-12">@lang('XLSX')</span>
                    <button
                        wire:click.prevent="exportToXLS"
                        x-on:click="open = false"
                        href="#"
                        class="btn btn-sm"
                    >
                        <span class="export-count text-xs">({{ $total }})</span>
                        @if (count($enabledFilters) === 0)
                            @lang('livewire-powergrid::datatable.labels.all')
                        @else
                            @lang('livewire-powergrid::datatable.labels.filtered')
                        @endif

                    </button>
                    @if ($checkbox)
                        <button
                            wire:click.prevent="exportToXLS(true)"
                            x-on:click="open = false"
                            x-bind:disabled="countChecked.length === 0"
                            :class="{ 'cursor-not-allowed': countChecked.length === 0 }"
                            class="btn btn-sm"
                        >
                            <span
                                class="export-count text-xs"
                                x-text="`(${countChecked.length})`"
                            ></span> @lang('livewire-powergrid::datatable.labels.selected')
                        </button>
                    @endif
                </div>
            @endif
            @if (in_array('csv', data_get($setUp, 'exportable.type')))
                <div class="flex items-center gap-2">
                    <span class="w-12">@lang('Csv')</span>
                    <button
                        wire:click.prevent="exportToCsv"
                        x-on:click="open = false"
                        class="btn btn-sm"
                    >
                        <span class="export-count text-xs">({{ $total }})</span>
                        @if (count($enabledFilters) === 0)
                            @lang('livewire-powergrid::datatable.labels.all')
                        @else
                            @lang('livewire-powergrid::datatable.labels.filtered')
                        @endif
                    </button>
                    @if ($checkbox)
                        <button
                            wire:click.prevent="exportToCsv(true)"
                            x-on:click="open = false"
                            :class="{ 'cursor-not-allowed': countChecked.length === 0 }"
                            class="btn btn-sm"
                        >
                            <span
                                class="export-count text-xs"
                                x-text="`(${countChecked.length})`"
                            ></span> @lang('livewire-powergrid::datatable.labels.selected')
                        </button>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
