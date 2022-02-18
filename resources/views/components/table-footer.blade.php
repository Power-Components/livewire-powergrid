@props([
'theme' => null,
'data' => null,
'actions' => null,
'checkbox' => null,
'primaryKey' => null,
'columns' => null,
'currentTable' => null,
'withoutPaginatedData' => null,
])
<tr class="{{ $theme->table->trBodyClass }}" style="{{ $theme->table->trBodyStyle }}">
    @if($checkbox)
        <td></td>
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
            style="{{ $column->hidden === true ? 'display:none': '' }}; {{ $theme->table->tdBodyStyleTotalColumns .' '.$column->bodyStyle ?? ''  }}">
            @if ($column->count['footer'])
                @php
                    $count = $withoutPaginatedData->collect()
                    ->reject(function($data) use($field) { return empty($data->$field); })
                    ->count($field);
                    
                    if (is_callable($column->count['formatter'])) {
                       $count = call_user_func($column->count['formatter'], $count);
                    }
                @endphp
                <span>{{ $column->count['label'] }}: {{ $count }}
                    </span>
                <br>
            @endif
            @if ($column->sum['footer'] && is_numeric($withoutPaginatedData[0][$field]))
                @php
                    $sum = $withoutPaginatedData->collect()->sum($field);
                    
                    if (is_callable($column->sum['formatter'])) {
                        $sum = call_user_func($column->sum['formatter'], $sum);
                    }
                @endphp
                <span>{{ $column->sum['label'] }}: {{ $sum }}</span>
                <br>
            @endif
            @if ($column->avg['footer'] && is_numeric($withoutPaginatedData[0][$column->dataField]))
                @php
                   $avg = round($withoutPaginatedData->collect()->avg($field), $column->avg['rounded']);
                    
                    if (is_callable($column->avg['formatter'])) {
                        $avg = call_user_func($column->avg['formatter'], $avg);
                    }
                @endphp
                <span>{{ $column->avg['label'] }}: {{ $avg }}</span>
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
