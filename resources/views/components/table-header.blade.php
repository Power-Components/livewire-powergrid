<tr class="{{ $theme->table->trBodyClass }}" style="{{ $theme->table->trBodyStyle }}">
    @if(data_get($setUp, 'detail.showCollapseIcon'))
        <td class="{{ $theme->table->tdBodyClass }}" style="{{ $theme->table->tdBodyStyle }}"></td>
    @endif
    @if($checkbox)
        <td  class="{{ $theme->table->tdBodyClass }}" style="{{ $theme->table->tdBodyStyle }}"></td>
    @endif
    @foreach ($columns as $column)
        @php
            if (filled($column->dataField) && str_contains($column->dataField, '.')) {
                $field = $column->field;
            } else
            if (filled($column->dataField) && !str_contains($column->dataField, '.')) {
                $field = $column->dataField;
            } else {
                $field = $column->field;
            }
        @endphp
        <td class="{{ $theme->table->tdBodyClassTotalColumns . ' '.$column->bodyClass ?? '' }}"
            style="{{ $column->hidden === true ? 'display:none': '' }}; {{$theme->table->tdBodyStyleTotalColumns . ' '.$column->bodyStyle ?? ''  }}">
            @if ($column->count['header'])
                <span>{{ $column->count['label'] }}: {{ $withoutPaginatedData->collect()->reject(function($data) use($field) { return empty($data->{$field} ?? $data[$field]); })->count($field) }}</span>
                <br>
            @endif
            @if ($column->sum['header'] && is_numeric($withoutPaginatedData[0][$field]))
                <span>{{ $column->sum['label'] }}: {{ round($withoutPaginatedData->collect()->sum($field), $column->sum['rounded']) }}</span>
                <br>
            @endif
            @if ($column->avg['header'] && is_numeric($withoutPaginatedData[0][$field]))<span>{{ $column->avg['label'] }}: {{ round($withoutPaginatedData->collect()->avg($field), $column->avg['rounded']) }}</span>
                <br>
            @endif
            @if ($column->min['header'] && is_numeric($withoutPaginatedData[0][$field]))
                    <span>{{ $column->min['label'] }}: {{ round($withoutPaginatedData->collect()->min($field), $column->min['rounded']) }}</span>
                    <br>
            @endif
            @if ($column->max['header'] && is_numeric($withoutPaginatedData[0][$field]))
                    <span>{{ $column->max['label'] }}: {{ round($withoutPaginatedData->collect()->max($field), $column->max['rounded']) }}</span>
                    <br>
            @endif
        </td>
    @endforeach
    @if(isset($actions) && count($actions))
        <th class="{{ $theme->table->thClass .' '. $column->headerClass }}" scope="col"
            style="{{ $theme->table->thStyle }}" colspan="{{count($actions)}}">
        </th>
    @endif
</tr>
