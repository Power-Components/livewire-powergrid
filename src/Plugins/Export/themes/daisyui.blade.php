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
    wire:key="export-dropdown-wrapper-{{ $tableName }}"
>
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
        x-data="pgExport"
        class="dropdown"
        style="position-anchor: --export-{{ $tableName }}"
    >
        <div class="flex flex-col gap-3 p-3 shadow bg-base-100 rounded-box w-max mt-2 text-sm">
            @if (in_array('xlsx', $types))
                <div class="flex items-center gap-2">
                    <span class="w-12 font-medium text-sm">@lang('XLSX')</span>
                    <button
                        wire:click.prevent="exportToXLS"
                        class="btn btn-sm font-normal bg-base-100 hover:bg-base-200 border-base-200"
                    >
                        <span class="export-count text-xs opacity-70">({{ $total }})</span>
                        @if ($enabledFiltersCount === 0)
                            @lang('livewire-powergrid::datatable.labels.all')
                        @else
                            @lang('livewire-powergrid::datatable.labels.filtered')
                        @endif
                    </button>
                    @if ($checkbox)
                        <button
                            wire:click.prevent="exportToXLS(true)"
                            x-bind:disabled="isEmpty()"
                            :class="disabledClass()"
                            class="btn btn-sm font-normal bg-base-100 hover:bg-base-200 border-base-200"
                        >
                            <span
                                class="export-count text-xs opacity-70"
                                x-text="countLabel()"
                            ></span> @lang('livewire-powergrid::datatable.labels.selected')
                        </button>
                    @endif
                </div>
            @endif
            @if (in_array('csv', $types))
                <div class="flex items-center gap-2">
                    <span class="w-12 font-medium text-sm">@lang('Csv')</span>
                    <button
                        wire:click.prevent="exportToCsv"
                        class="btn btn-sm font-normal bg-base-100 hover:bg-base-200 border-base-200"
                    >
                        <span class="export-count text-xs opacity-70">({{ $total }})</span>
                        @if ($enabledFiltersCount === 0)
                            @lang('livewire-powergrid::datatable.labels.all')
                        @else
                            @lang('livewire-powergrid::datatable.labels.filtered')
                        @endif
                    </button>
                    @if ($checkbox)
                        <button
                            wire:click.prevent="exportToCsv(true)"
                            x-bind:disabled="isEmpty()"
                            :class="disabledClass()"
                            class="btn btn-sm font-normal bg-base-100 hover:bg-base-200 border-base-200"
                        >
                            <span
                                class="export-count text-xs opacity-70"
                                x-text="countLabel()"
                            ></span> @lang('livewire-powergrid::datatable.labels.selected')
                        </button>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
