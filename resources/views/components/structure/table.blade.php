@php
    use PowerComponents\Turbine\DataSource\DataTransformer;
    use PowerComponents\LivewirePowerGrid\PowerGridComponent;

    $dataTransformer = new DataTransformer($this);

    /** @var PowerGridComponent $this */
@endphp

<div
    class="pg-container {{ theme('layout.container') }}"
    @if ($deferLoading) wire:init="fetchDatasource" @endif
>
    @once
        {!! $this->renderPluginAssets() !!}
    @endonce

    <div
        id="power-grid-table-container-{{ $tableName }}"
        class="pg-table-wrapper {{ theme('layout.wrapper') }}"
    >
        @include(theme_view('header'), [
            'enabledFilters' => $enabledFilters,
            '__partial' => $this,
        ])

        @if ($this->usesFilterPanel() && ! $this->filterBuilderHidesDefaultFilters())
            @php
                $filtersFromColumns = collect($columns)
                    ->filter(fn($column) => filled(data_get($column, 'filters')));
            @endphp

            @include(theme_view($this->filterPanelView()), [
                '__partial' => $this,
                'tableName' => $tableName,
                'filtersFromColumns' => $filtersFromColumns,
            ])
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
                <div>
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
            $wire.on('pgRowTemplates', function (event) {
                let rowTemplates = Array.isArray(event) ? event[0] : event;
                window['pgRowTemplates_' + $wire.id] = JSON.parse(rowTemplates);
            });
        </script>
    @endscript
</div>
