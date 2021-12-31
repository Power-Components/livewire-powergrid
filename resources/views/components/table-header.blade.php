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
        @if($column->hidden === false)
            <td class="{{ $theme->table->tdBodyClassTotalColumns . ' '.$column->bodyClass ?? '' }}"
                style=" {{$theme->table->tdBodyStyleTotalColumns . ' '.$column->bodyStyle ?? ''  }}">
                @if ($column->count['header'])
                    <span>{{ $column->count['label'] }}: {{ $withoutPaginatedData->collect()->count($field) }}</span>
                    <br>
                @endif
                @if ($column->sum['header'] && is_numeric($withoutPaginatedData[0][$field]))
                    <span>{{ $column->sum['label'] }}: {{ $withoutPaginatedData->collect()->sum($field) }}</span>
                    <br>
                @endif
                @if ($column->avg['header'] && is_numeric($withoutPaginatedData[0][$field]))
                    <span>{{ $column->avg['label'] }}: {{ round($withoutPaginatedData->collect()->avg($field), $column->avg['rounded']) }}</span>
                    <br>
                @endif
            </td>
        @else
            <td></td>
        @endif
    @endforeach
    @if(isset($actions) && count($actions))
        <th class="{{ $theme->table->thClass .' '. $column->headerClass }}" scope="col"
            style="{{ $theme->table->thStyle }}" colspan="{{count($actions)}}">
        </th>
    @endif
</tr>
