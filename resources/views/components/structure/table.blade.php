@php
    use PowerComponents\LivewirePowerGrid\DataSource\DataTransformer;
    use PowerComponents\LivewirePowerGrid\PowerGridComponent;

    $dataTransformer = new DataTransformer($this);

    /** @var PowerGridComponent $this */
@endphp

<div
    class="pg-container {{ theme('layout.container') }}"
    @if ($deferLoading) wire:init="fetchDatasource" @endif
>
    <div
        id="power-grid-table-container-{{ $tableName }}"
        class="pg-table-wrapper {{ theme('layout.wrapper') }}"
    >
        @include(theme_view('header'), [
            'enabledFilters' => $enabledFilters,
            '__partial' => $this,
        ])

        @if (config('livewire-powergrid.filter') === 'outside')
            <div wire:partial="pg-filters-{{ $tableName }}" class="{{ theme('layout.outsideFilters') }}">
                @php
                    $filtersFromColumns = collect($columns)
                        ->filter(fn($column) => filled(data_get($column, 'filters')));
                @endphp

                @includeWhen(
                    $filtersFromColumns->count() > 0,
                    theme_view('filter'),
                    [
                        '__partial' => $this,
                        'tableName' => $tableName,
                        'filtersFromColumns' => $filtersFromColumns,
                    ]
                )
            </div>
        @endif

        <div>
            <div
                @class([
                    'pg-table-responsive',
                    'overflow-auto' => $readyToLoad,
                    'overflow-hidden' => !$readyToLoad,
                    theme('table.layout.container'),
                ])
                @isset($this->setUp['responsive']) x-data="pgResponsive" @endisset
            >
                <div x-data="{ expandedId: null }">
                    <table
                        id="table_base_{{ $tableName }}"
                        class="pg-table {{ theme('table.layout.table') }}"
                    >
                        @include(theme_view('table.thead'), [
                            'loading' => !$readyToLoad,
                            '__partial' => $this,
                        ])

                        @if ($readyToLoad)
                            @include(theme_view('table.tbody'), ['__partial' => $this])
                        @else
                            @include(theme_view('table.tbody'), ['__partial' => $this, 'loading' => true])
                        @endif
                    </table>
                </div>
            </div>

            @include(theme_view('footer'), ['__partial' => $this])
        </div>
    </div>

    @script
        <script>
            this.$js('pgRowTemplates', (rowTemplates) => {
                window['pgRowTemplates_' + $wire.id] = JSON.parse(rowTemplates);
            })
        </script>
    @endscript
</div>
