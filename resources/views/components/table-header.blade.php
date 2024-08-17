{{-- blade-formatter-disable --}}
<tr class="{{ theme_style($theme, 'table.body.tr'). ' '.theme_style($theme, 'table.body.trSummarize') }}"
    style="{{ theme_style($theme, 'table.body.tr.1'). ' '.theme_style($theme, 'table.body.trSummarize.1') }}">
    @if(data_get($setUp, 'detail.showCollapseIcon'))
        <td class="{{ theme_style($theme, 'table.body.td') }}" style="{{ theme_style($theme, 'table.body.td.1') }}"></td>
    @endif
    @if($checkbox)
        <td class="{{ theme_style($theme, 'table.body.td') }}" style="{{ theme_style($theme, 'table.body.td.1') }}"></td>
    @endif
    @foreach ($this->visibleColumns as $column)
        <td class="{{ theme_style($theme, 'table.body.tdSummarize') . ' '.data_get($column, 'bodyClass') ?? '' }}"
            style="{{ data_get($column, 'hidden') === true ? 'display:none': '' }}; {{ theme_style($theme, 'table.body.tdSummarize') . ' '.data_get($column, 'bodyStyle') ?? ''  }}">
            @include('livewire-powergrid::components.summarize', [
                'sum' => data_get($column, 'sum.header') ? data_get($column, 'summarize.sum') : null,
                'labelSum' => data_get($column, 'sum.label'),

                'count' => data_get($column, 'count.header') ? data_get($column, 'summarize.count') : null,
                'labelCount' => data_get($column, 'count.label'),

                'min' => data_get($column, 'min.header') ? data_get($column, 'summarize.min') : null,
                'labelMin' => data_get($column, 'min.label'),

                'max' => data_get($column, 'max.header') ? data_get($column, 'summarize.max') : null,
                'labelMax' => data_get($column, 'max.label'),

                'avg' => data_get($column, 'avg.header') ? data_get($column, 'summarize.avg') : null,
                'labelAvg' => data_get($column, 'avg.label'),
            ])
        </td>
    @endforeach
    @if(isset($actions) && count($actions))
        <th class="{{ theme_style($theme, 'table.header.th') .' '. data_get($column, 'headerClass') }}" scope="col"
            style="{{ theme_style($theme, 'table.header.th.1') }}" colspan="{{count($actions)}}">
        </th>
    @endif
</tr>
{{-- blade-formatter-enable --}}
