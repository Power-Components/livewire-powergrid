<div x-data="{ countChecked: @entangle('checkboxValues').live }">
    @if (filled(data_get($setUp, 'exportable')))
        <flux:dropdown>
            <flux:button
                icon:trailing="chevron-down"
                class="{{ theme('header.export.button') }}"
            >
                <x-livewire-powergrid::icons.download class="h-5 w-5" />
            </flux:button>

            <flux:menu>
                @if (in_array('xlsx', data_get($setUp, 'exportable.type')))
                    <flux:menu.item wire:click.prevent="exportToXLS">
                        @lang('XLSX') -
                        @if (count($enabledFilters) === 0)
                            @lang('livewire-powergrid::datatable.labels.all')
                        @else
                            @lang('livewire-powergrid::datatable.labels.filtered')
                        @endif
                        ({{ $this->total }})
                    </flux:menu.item>
                    @if ($checkbox)
                        <flux:menu.item
                            wire:click.prevent="exportToXLS(true)"
                            x-bind:disabled="countChecked.length === 0"
                        >
                            @lang('XLSX') - @lang('livewire-powergrid::datatable.labels.selected')
                            <span x-text="`(${countChecked.length})`"></span>
                        </flux:menu.item>
                    @endif
                @endif

                @if (in_array('csv', data_get($setUp, 'exportable.type')))
                    <flux:menu.item wire:click.prevent="exportToCsv">
                        @lang('CSV') -
                        @if (count($enabledFilters) === 0)
                            @lang('livewire-powergrid::datatable.labels.all')
                        @else
                            @lang('livewire-powergrid::datatable.labels.filtered')
                        @endif
                        ({{ $this->total }})
                    </flux:menu.item>
                    @if ($checkbox)
                        <flux:menu.item
                            wire:click.prevent="exportToCsv(true)"
                            x-bind:disabled="countChecked.length === 0"
                        >
                            @lang('CSV') - @lang('livewire-powergrid::datatable.labels.selected')
                            <span x-text="`(${countChecked.length})`"></span>
                        </flux:menu.item>
                    @endif
                @endif
            </flux:menu>
        </flux:dropdown>
    @endif
</div>
