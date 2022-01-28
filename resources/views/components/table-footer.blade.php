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
                <span>{{ $column->count['label'] }}: {{ $withoutPaginatedData->collect()
                    ->reject(function($data) use($field) { return empty($data->$field); })
                    ->count($field) }}
                    </span>
                <br>
            @endif
            @if ($column->sum['footer'] && is_numeric($withoutPaginatedData[0][$field]))
                <span>{{ $column->sum['label'] }}: {{ $withoutPaginatedData->collect()->sum($field) }}</span>
                <br>
            @endif
            @if ($column->avg['footer'] && is_numeric($withoutPaginatedData[0][$column->dataField]))
                <span>{{ $column->avg['label'] }}: {{ $withoutPaginatedData->collect()->avg($field) }}</span>
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
