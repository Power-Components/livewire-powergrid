{{-- blade-formatter-disable --}}
<tr class="{{ $theme->table->trBodyClass. ' '.$theme->table->trBodyClassTotalColumns }}"
    style="{{ $theme->table->trBodyStyle. ' '.$theme->table->trBodyStyleTotalColumns }}">
    @if(data_get($setUp, 'detail.showCollapseIcon'))
        <td class="{{ $theme->table->tdBodyClass }}" style="{{ $theme->table->tdBodyStyle }}"></td>
    @endif
    @if($checkbox)
        <td class="{{ $theme->table->tdBodyClass }}" style="{{ $theme->table->tdBodyStyle }}"></td>
    @endif
    @foreach ($this->visibleColumns as $column)
        <td class="{{ $theme->table->tdBodyClassTotalColumns . ' '.$column->bodyClass ?? '' }}"
            style="{{ $column->hidden === true ? 'display:none': '' }}; {{$theme->table->tdBodyStyleTotalColumns . ' '.$column->bodyStyle ?? ''  }}">
            @include('livewire-powergrid::components.summarize', [
                'sum' => $column->sum['header'] ? data_get($column, 'summarize.sum') : null,
                'labelSum' => $column->sum['label'],

                'count' => $column->count['header'] ? data_get($column, 'summarize.count') : null,
                'labelCount' => $column->count['label'],

                'min' => $column->min['header'] ? data_get($column, 'summarize.min') : null,
                'labelMin' => $column->min['label'],

                'max' => $column->max['header'] ? data_get($column, 'summarize.max') : null,
                'labelMax' => $column->max['label'],

                'avg' => $column->avg['header'] ? data_get($column, 'summarize.avg') : null,
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
