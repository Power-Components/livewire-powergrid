@props([
    'tableName' => null,
    '__partial' => null,
    'loading' => false,
])

@php
    $__partial = $__partial ?? $this;
    $tableName = $tableName ?? $__partial->tableName;
@endphp
<tbody
    wire:partial="pg-tbody-{{ $tableName }}"
    wire:key="tbody-{{ $tableName }}"
    class="pg-tbody {{ theme('table.layout.tbody') }}"
>
    @if ($loading)
        @include(theme_view('table.header'), ['loading' => true, '__partial' => $__partial])
    @else
        @includeWhen($__partial->hasColumnFilters && ! $__partial->filterBuilderHidesDefaultFilters(), theme_view('table.inline-filters'), [
            '__partial' => $__partial,
            'tableName' => $tableName,
        ])

        @if (count($__partial->records) === 0)
            @include(theme_view('table.th-empty'), [
                '__partial' => $__partial,
            ])
        @else
            @includeWhen($__partial->headerTotalColumn, theme_view('table.summarize'), [
                '__partial' => $__partial,
                'isHeader' => true,
            ])

            @if (isset($__partial->setUp['detail']))
                @foreach ($__partial->records as $row)
                    @php
                        $rowId = data_get($row, $__partial->realPrimaryKey);
                        $class = theme('table.layout.tr');
                        if ($loop->odd) {
                            $class .= ' ' . theme('table.layout.tr_striped');
                        } else {
                            $class .= ' ' . theme('table.layout.tr_not_striped');
                        }
                    @endphp

                    <tr
                        wire:key="row-{{ $rowId }}"
                        class="{{ $class }}"
                        x-data="pgRowAttributes({ rowId: @js($rowId), rules: @js($row->__powergrid_rules) })"
                        x-bind="getAttributes"
                    >
                        @include(theme_view('table.row'), [
                            'rowIndex' => $loop->index + 1,
                            '__partial' => $__partial,
                        ])
                    </tr>

                    @php
                        $hasDetailView = (bool) data_get(
                            collect($row->__powergrid_rules)->where('apply', true)->last(),
                            'detailView',
                        );

                        if ($hasDetailView) {
                            $detailView = data_get($row->__powergrid_rules, '0.detailView');
                            $rulesValues = data_get($row->__powergrid_rules, '0.options', []);
                        } else {
                            $detailView = data_get($__partial->setUp, 'detail.view');
                            $rulesValues = data_get($__partial->setUp, 'detail.options', []);
                        }
                    @endphp

                    @php
                        if ($row instanceof stdClass) {
                            $row = collect($row);
                        }
                    @endphp

                    <livewire:powergrid-detail
                        wire:key="powergrid-detail-{{ $rowId }}"
                        :view="$detailView"
                        :options="$rulesValues"
                        :row-id="$rowId"
                        tr-class="{{ $class }}"
                        :row="(object) $row->toArray()"
                        :single-expand="data_get($__partial->setUp, 'detail.singleExpand', false)"
                        :table-name="$tableName"
                    />

                    @includeWhen(isset($__partial->setUp['responsive']),
                        theme_view('table.responsive-container'), [
                            '__partial' => $__partial,
                        ])
                @endforeach
            @else
                @foreach ($__partial->records as $row)
                    @php
                        $rowId = data_get($row, $__partial->realPrimaryKey);
                        $class = theme('table.layout.tr');
                        if ($loop->odd) {
                            $class .= ' ' . theme('table.layout.tr_striped');
                        } else {
                            $class .= ' ' . theme('table.layout.tr_not_striped');
                        }
                    @endphp

                    <tr
                        wire:key="row-{{ $rowId }}"
                        class="{{ $class }}"
                        x-data="pgRowAttributes({ rowId: @js($rowId), rules: @js($row->__powergrid_rules) })"
                        x-bind="getAttributes"
                    >
                        @include(theme_view('table.row'), [
                            'rowIndex' => $loop->index + 1,
                            '__partial' => $__partial,
                        ])
                    </tr>

                    @includeWhen(isset($__partial->setUp['responsive']),
                        theme_view('expand-container'), [
                            '__partial' => $__partial,
                        ])
                @endforeach
            @endif

            @includeWhen($__partial->footerTotalColumn, theme_view('table.summarize'), [
                '__partial' => $__partial,
                'isHeader' => false,
            ])
        @endif
    @endif
</tbody>
