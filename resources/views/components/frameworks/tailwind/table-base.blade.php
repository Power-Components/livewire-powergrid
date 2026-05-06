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
        class=""
    >
        <div
            id="power-grid-table-base"
            class=""
        >
            @include(theme_view('header'), [
                'enabledFilters' => $enabledFilters,
            ])

            @if (config('livewire-powergrid.filter') === 'outside')
                @php
                    $filtersFromColumns = $columns
                        ->filter(fn($column) => filled(data_get($column, 'filters')));
                @endphp

                @includeWhen(
                    $filtersFromColumns->count() > 0,
                    'livewire-powergrid::components.frameworks.tailwind.filter'
                )
            @endif

            <div
                @class([
                    'overflow-auto' => $readyToLoad,
                    'overflow-hidden' => !$readyToLoad,
                    theme('table.layout.container'),
                ])
            >
                @include($table)
            </div>

            @include(theme_view('footer'))
        </div>
    </div>
</div>
