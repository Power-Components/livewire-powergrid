@props([
    'element' => [],
    'tableName' => null,
    'types' => [],
    'total' => 0,
    'enabledFiltersCount' => 0,
    'checkbox' => false,
])

<div
    id="pg-header-export"
    x-data="pgExport"
    wire:key="export-dropdown-{{ $tableName }}"
>
    <flux:dropdown>
        <flux:button
            variant="filled"
            class="{{ theme('header.export.button', theme('header.layout.actions')) }}"
            title="{{ data_get($element, 'title') }}"
            aria-label="{{ data_get($element, 'title') }}"
        >
            {!! data_get($element, 'iconHtml') !!}
            @if (data_get($element, 'showLabel'))
                <span class="{{ theme('header.export.label') }}">{{ data_get($element, 'title') }}</span>
            @endif
        </flux:button>

        <flux:menu class="{{ theme('header.export.menu') }}">
            @if (in_array('xlsx', $types))
                <flux:menu.item wire:click.prevent="exportToXLS">
                    @lang('XLSX') -
                    @if ($enabledFiltersCount === 0)
                        @lang('livewire-powergrid::datatable.labels.all')
                    @else
                        @lang('livewire-powergrid::datatable.labels.filtered')
                    @endif
                    ({{ $total }})
                </flux:menu.item>
                @if ($checkbox)
                    <flux:menu.item
                        wire:click.prevent="exportToXLS(true)"
                        x-bind:disabled="isEmpty()"
                    >
                        @lang('XLSX') - @lang('livewire-powergrid::datatable.labels.selected')
                        <span x-text="countLabel()"></span>
                    </flux:menu.item>
                @endif
            @endif

            @if (in_array('csv', $types))
                <flux:menu.item wire:click.prevent="exportToCsv">
                    @lang('CSV') -
                    @if ($enabledFiltersCount === 0)
                        @lang('livewire-powergrid::datatable.labels.all')
                    @else
                        @lang('livewire-powergrid::datatable.labels.filtered')
                    @endif
                    ({{ $total }})
                </flux:menu.item>
                @if ($checkbox)
                    <flux:menu.item
                        wire:click.prevent="exportToCsv(true)"
                        x-bind:disabled="isEmpty()"
                    >
                        @lang('CSV') - @lang('livewire-powergrid::datatable.labels.selected')
                        <span x-text="countLabel()"></span>
                    </flux:menu.item>
                @endif
            @endif
        </flux:menu>
    </flux:dropdown>
</div>
