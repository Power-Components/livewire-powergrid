{{-- blade-formatter-disable --}}
<tr class="{{ data_get($theme, 'table.trBodyClass'). ' '.data_get($theme, 'table.trBodyClassTotalColumns') }}"
    style="{{ data_get($theme, 'table.trBodyStyle'). ' '.data_get($theme, 'table.trBodyStyleTotalColumns') }}">
    @if(data_get($setUp, 'detail.showCollapseIcon'))
        <td class="{{ data_get($theme, 'table.tdBodyClass') }}" style="{{ data_get($theme, 'table.tdBodyStyle') }}"></td>
    @endif
    @if($checkbox)
        <td class="{{ data_get($theme, 'table.tdBodyClass') }}" style="{{ data_get($theme, 'table.tdBodyStyle') }}"></td>
    @endif
    @foreach ($this->visibleColumns as $column)
        <td class="{{ data_get($theme, 'table.tdBodyClassTotalColumns') . ' '.data_get($column, 'bodyClass') ?? '' }}"
            style="{{ data_get($column, 'hidden') === true ? 'display:none': '' }}; {{ data_get($theme, 'table.tdBodyStyleTotalColumns') . ' '.data_get($column, 'bodyStyle') ?? ''  }}">
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
        <th class="{{ data_get($theme, 'table.thClass') .' '. data_get($column, 'headerClass') }}" scope="col"
            style="{{ data_get($theme, 'table.thStyle') }}" colspan="{{count($actions)}}">
        </th>
    @endif
</tr>
{{-- blade-formatter-enable --}}
