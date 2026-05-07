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
        ])

        @if (config('livewire-powergrid.filter') === 'outside')
            @php
                $filtersFromColumns = collect($columns)
                    ->filter(fn($column) => filled(data_get($column, 'filters')));
            @endphp

            @includeWhen(
                $filtersFromColumns->count() > 0,
                theme_view('filter')
            )
        @endif

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
                    <thead class="pg-thead {{ theme('table.layout.thead') }}">
                        @include(theme_view('table.header'), [
                            'loading' => !$readyToLoad,
                        ])
                    </thead>

                    @if ($readyToLoad)
                        <tbody class="pg-tbody {{ theme('table.layout.tbody') }}">
                            @includeWhen($this->hasColumnFilters, theme_view('table.inline-filters'))

                            @if (count($this->records) === 0)
                                @include(theme_view('table.th-empty'))
                            @else
                                @includeWhen($headerTotalColumn, theme_view('table.summarize'))

                                @if (isset($setUp['detail']))
                                    @foreach ($this->records as $row)
                                        @php
                                            $rowId = data_get($row, $this->realPrimaryKey);
                                            $class = theme('table.layout.tr');
                                        @endphp

                                        <tbody
                                            wire:key="tbody-{{ $rowId }}"
                                            class="{{ $class }}"
                                            x-data="pgRowAttributes({ rowId: @js($rowId), rules: @js($row->__powergrid_rules) })"
                                            x-bind="getAttributes"
                                        >
                                            @include(theme_view('table.row'), [
                                                'rowIndex' => $loop->index + 1,
                                            ])

                                            @php
                                                $hasDetailView = (bool) data_get(
                                                    collect($row->__powergrid_rules)->where('apply', true)->last(),
                                                    'detailView',
                                                );

                                                if ($hasDetailView) {
                                                    $detailView = data_get($row->__powergrid_rules, '0.detailView');
                                                    $rulesValues = data_get($row->__powergrid_rules, '0.options', []);
                                                } else {
                                                    $detailView = data_get($setUp, 'detail.view');
                                                    $rulesValues = data_get($setUp, 'detail.options', []);
                                                }
                                            @endphp

                                            @php
                                                if ($row instanceof stdClass) {
                                                    $row = collect($row);
                                                }
                                            @endphp

                                            <livewire:powergrid-detail
                                                key="powergrid-detail-{{ $rowId }}"
                                                :view="$detailView"
                                                :options="$rulesValues"
                                                :row-id="$rowId"
                                                tr-class="{{ $class }}"
                                                :row="(object) $row->toArray()"
                                                :collapse-others="data_get($setUp, 'detail.collapseOthers', false)"
                                                :table-name="$tableName"
                                            />
                                        </tbody>

                                        @includeWhen(isset($setUp['responsive']),
                                            theme_view('table.responsive-container'))
                                    @endforeach
                                @else
                                    @foreach ($this->records as $row)
                                        @php
                                            $rowId = data_get($row, $this->realPrimaryKey);
                                            $class = theme('table.layout.tr');
                                        @endphp

                                        <tr
                                            wire:replace.self
                                            class="{{ $class }}"
                                            x-data="pgRowAttributes({ rowId: @js($rowId), rules: @js($row->__powergrid_rules) })"
                                            x-bind="getAttributes"
                                        >
                                            @include(theme_view('table.row'), [
                                                'rowIndex' => $loop->index + 1,
                                            ])
                                        </tr>

                                        @includeWhen(isset($setUp['responsive']),
                                            theme_view('expand-container'))
                                    @endforeach
                                @endif

                                @includeWhen($footerTotalColumn, theme_view('table.summarize'))
                            @endif
                        </tbody>
                    @else
                        <tbody class="pg-tbody {{ theme('table.layout.tbody') }}">
                            @include(theme_view('table.header'), ['loading' => true])
                        </tbody>
                    @endif
                </table>
            </div>
        </div>

        @include(theme_view('footer'))
    </div>

    @script
        <script>
            this.$js('pgRowTemplates', (rowTemplates) => {
                window['pgRowTemplates_' + $wire.id] = JSON.parse(rowTemplates);
            })
        </script>
    @endscript
</div>
