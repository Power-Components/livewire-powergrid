{{-- blade-formatter-disable --}}
@props([
    '__partial' => null,
    'isHeader' => true,
])

@php
    $__partial = $__partial ?? $this;
    $flag = $isHeader ? 'header' : 'footer';
@endphp

<tr
    wire:key="summarize-{{ $isHeader ? 'header' : 'footer' }}-{{ $__partial->tableName }}"
    class="{{ theme('table.layout.tr') }}"
>
    @if(data_get($__partial->setUp, 'detail.showCollapseIcon'))
        <td class="{{ theme('table.layout.td') }}"></td>
    @endif
    @if($__partial->checkbox)
        <td class="{{ theme('table.layout.td') }}"></td>
    @endif
    @foreach ($__partial->visibleColumns as $column)
        @php
            $customSummaries = collect(data_get($column, 'properties.summarize.custom') ?? [])
                ->filter(fn ($meta) => data_get($meta, $flag))
                ->map(fn ($meta, $key) => [
                    'label' => data_get($meta, 'label'),
                    'value' => data_get($column, "properties.summarize_values.custom.$key"),
                ])
                ->values()
                ->all();
        @endphp
        <td class="{{ theme('table.layout.td') . ' '.data_get($column, 'bodyClass') ?? '' }}"
            style="{{ data_get($column, 'hidden') === true ? 'display:none;': '' }} {{ data_get($column, 'bodyStyle') ?? ''  }}">
            @include(theme_view('summarize'), [
                'sum' => data_get($column, "properties.summarize.sum.$flag") ? data_get($column, 'properties.summarize_values.sum') : null,
                'labelSum' => data_get($column, 'properties.summarize.sum.label'),

                'count' => data_get($column, "properties.summarize.count.$flag") ? data_get($column, 'properties.summarize_values.count') : null,
                'labelCount' => data_get($column, 'properties.summarize.count.label'),

                'min' => data_get($column, "properties.summarize.min.$flag") ? data_get($column, 'properties.summarize_values.min') : null,
                'labelMin' => data_get($column, 'properties.summarize.min.label'),

                'max' => data_get($column, "properties.summarize.max.$flag") ? data_get($column, 'properties.summarize_values.max') : null,
                'labelMax' => data_get($column, 'properties.summarize.max.label'),

                'avg' => data_get($column, "properties.summarize.avg.$flag") ? data_get($column, 'properties.summarize_values.avg') : null,
                'labelAvg' => data_get($column, 'properties.summarize.avg.label'),

                'custom' => $customSummaries,
            ])
        </td>
    @endforeach
    @if(isset($actions) && count($actions))
        <th class="{{ theme('table.layout.th') . ' ' . data_get($column, 'headerClass') }}" scope="col"
           colspan="{{count($actions)}}">
        </th>
    @endif
</tr>
{{-- blade-formatter-enable --}}
