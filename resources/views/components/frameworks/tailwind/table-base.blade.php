<div
    class="flex flex-col"
    @if ($deferLoading) wire:init="fetchDatasource" @endif
>
    <div
        id="power-grid-table-container"
        class="{{ theme_style($theme, 'table.layout.container') }}"
        style="{{ theme_style($theme, 'table.layout.container.1') }}"
    >
        <div
            id="power-grid-table-base"
            class="{{ theme_style($theme, 'table.layout.base') }}"
            style="{{ theme_style($theme, 'table.layout.base.1') }}"
        >
            @include(theme_style($theme, 'layout.header'), [
                'enabledFilters' => $enabledFilters,
            ])

            @if (config('livewire-powergrid.filter') === 'outside')
                @php
                    $filtersFromColumns = collect($columns)
                        ->filter(fn($column) => filled(data_get($column, 'filters')));
                @endphp

                @if ($filtersFromColumns->count() > 0)
                    <x-livewire-powergrid::frameworks.tailwind.filter
                        :enabled-filters="$enabledFilters"
                        :tableName="$tableName"
                        :columns="$columns"
                        :filtersFromColumns="$filtersFromColumns"
                        :theme="$theme"
                    />
                @endif
            @endif

            <div
                @class([
                    'overflow-auto' => $readyToLoad,
                    'overflow-hidden' => !$readyToLoad,
                    theme_style($theme, 'table.layout.div'),
                ])
                style="{{ theme_style($theme, 'table.layout.div.1') }}"
            >
                @include($table)
            </div>

            @include(theme_style($theme, 'footer.view'))
        </div>
    </div>
</div>
