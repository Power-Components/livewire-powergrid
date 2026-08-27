@props([
    'tableName' => null,
    '__partial' => null,
    'loading' => false,
])

@use('PowerComponents\LivewirePowerGrid\Support\RowRenderer')

@php
    $__partial = $__partial ?? $this;
    $tableName = $tableName ?? $__partial->tableName;
    $rowRenderer = RowRenderer::canRenderDirect($__partial) ? new RowRenderer($__partial) : null;
    $responsive = isset($__partial->setUp['responsive']);
    $hasDetail = isset($__partial->setUp['detail']);
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

                <tr {{ $__partial->rowAttributes($row, new \Illuminate\View\ComponentAttributeBag([
                    'wire:key' => 'row-' . $rowId,
                    'data-pg-row-id' => $rowId,
                    'class' => $class,
                ])) }}>
                    @if ($rowRenderer)
                        {!! $rowRenderer->render($row, $loop->index + 1, null, null, $rowId) !!}
                    @else
                        @include(theme_view('table.row'), [
                            'rowIndex' => $loop->index + 1,
                            '__partial' => $__partial,
                        ])
                    @endif
                </tr>

                @if ($hasDetail && $__partial->isDetailOpen($rowId))
                    @php
                        $detail = $__partial->detailForRow($row);
                    @endphp
                    @if (filled($detail['view']))
                        <tr
                            wire:key="powergrid-detail-{{ $rowId }}"
                            class="{{ $class }}"
                        >
                            <td colspan="999">
                                @include($detail['view'], [
                                    'id' => $rowId,
                                    'options' => $detail['options'] ?? [],
                                    'row' => $row,
                                ])
                            </td>
                        </tr>
                    @endif
                @endif

                @if ($responsive)
                    @if ($rowRenderer)
                        {!! $rowRenderer->renderExpandRow($rowId) !!}
                    @else
                        @include(theme_view('table.responsive-container'), ['__partial' => $__partial])
                    @endif
                @endif
            @endforeach

            @includeWhen($__partial->footerTotalColumn, theme_view('table.summarize'), [
                '__partial' => $__partial,
                'isHeader' => false,
            ])
        @endif
    @endif
</tbody>
