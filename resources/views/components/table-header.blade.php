{{-- blade-formatter-disable --}}
<tr class="{{ $theme->table->trBodyClass }}" style="{{ $theme->table->trBodyStyle }}">
    @if(data_get($setUp, 'detail.showCollapseIcon'))
        <td class="{{ $theme->table->tdBodyClass }}" style="{{ $theme->table->tdBodyStyle }}"></td>
    @endif
    @if($checkbox)
        <td  class="{{ $theme->table->tdBodyClass }}" style="{{ $theme->table->tdBodyStyle }}"></td>
    @endif
    @foreach ($columns as $column)
        <td class="{{ $theme->table->tdBodyClassTotalColumns . ' '.$column->bodyClass ?? '' }}"
            style="{{ $column->hidden === true ? 'display:none': '' }}; {{$theme->table->tdBodyStyleTotalColumns . ' '.$column->bodyStyle ?? ''  }}">
            @include('livewire-powergrid::components.summarize', [
                'sum' => data_get($column, 'summarize.sum'),
                'labelSum' => $column->sum['label'],
                'count' => data_get($column, 'summarize.count'),
                'labelCount' => $column->count['label'],
                'min' => data_get($column, 'summarize.min'),
                'labelMin' => $column->min['label'],
                'max' => data_get($column, 'summarize.max'),
                'labelMax' => $column->max['label'],
                'avg' => data_get($column, 'summarize.avg'),
                'labelAvg' => $column->avg['label'],
            ])
        </td>
    @endforeach
    @if(isset($actions) && count($actions))
        <th class="{{ $theme->table->thClass .' '. $column->headerClass }}" scope="col"
            style="{{ $theme->table->thStyle }}" colspan="{{count($actions)}}">
        </th>
    @endif
</tr>
{{-- blade-formatter-enable --}}
