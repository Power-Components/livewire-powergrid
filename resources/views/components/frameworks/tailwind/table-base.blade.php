@php
    $columns = collect($columns)->map(function ($column) {
        return data_forget($column, 'rawQueries');
    });
@endphp

<div
    class="flex flex-col"
    @if ($deferLoading) wire:init="fetchDatasource" @endif
>
    <div
        id="power-grid-table-container"
        class="{{ theme_style($theme, 'table.layout.container') }}"
    >
        <div
            id="power-grid-table-base"
            class="{{ theme_style($theme, 'table.layout.base') }}"
        >
            @include(theme_style($theme, 'layout.header'), [
                'enabledFilters' => $enabledFilters,
            ])

            @if (config('livewire-powergrid.filter') === 'outside')
                @php
                    $filtersFromColumns = $columns
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
            >
                @include($table)
            </div>

            @include(theme_style($theme, 'footer.view'))
        </div>
    </div>
</div>
