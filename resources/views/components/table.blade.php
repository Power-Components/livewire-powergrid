@php
    use PowerComponents\LivewirePowerGrid\DataSource\DataTransformer;
    use PowerComponents\LivewirePowerGrid\PowerGridComponent;

    $dataTransformer = new DataTransformer($this);

    /** @var PowerGridComponent $this */

@endphp
<x-livewire-powergrid::table-base
    :$readyToLoad
    :$tableName
>
    <x-slot:header>
        @include(theme_view('table.header'))
    </x-slot:header>

    <x-slot:loading>
        @include(theme_view('table.header'), ['loading' => true])
    </x-slot:loading>

    <x-slot:body>
        @includeWhen($this->hasColumnFilters, theme_view('table.inline-filters'))

        @if (count($this->records) === 0)
            @include(theme_view('table.th-empty'))
        @else
            @includeWhen($headerTotalColumn, theme_view('table.header-summarize'))

                @if (isset($setUp['detail']))
                    @foreach ($this->records as $row)
                        @php
                            $rowId = data_get($row, $this->realPrimaryKey);
                            $class = theme('table.body.tr.wrapper');
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
                            $class = theme('table.body.tr.wrapper');
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
                            'livewire-powergrid::components.expand-container')
                    @endforeach
                @endif

            @includeWhen($footerTotalColumn, theme_view('table.footer-summarize'))
        @endif
    </x-slot:body>
</x-livewire-powergrid::table-base>
