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
        @if($column->hidden === false)
            <td class="{{ $theme->table->tdBodyClass . ' '.$theme->table->tdBodyClassTotalColumns ?? '' . ' '.$column->bodyClass ?? '' }}"
                style=" {{ $theme->table->tdBodyStyle . ' '.$theme->table->tdBodyStyleTotalColumns ?? ''. ' '.$column->bodyStyle ?? ''  }}">
                @if (!empty($column->count) && $column->count['header'])
                <span class="">{{ $column->count['label'] }}: {{ $withoutPaginatedData->collect()->count($column->dataField) }}</span><br>
                @endif
                @if (!empty($column->sum) && $column->sum['header'] && is_numeric($withoutPaginatedData[0][$column->dataField]))
                    <span class="">{{ $column->sum['label'] }}: {{ $withoutPaginatedData->collect()->sum($column->dataField) }}</span><br>
                @endif
                @if (!empty($column->avg) && $column->avg['header'] && is_numeric($withoutPaginatedData[0][$column->dataField]))
                    <span class="">{{ $column->avg['label'] }}: {{ $withoutPaginatedData->collect()->avg($column->dataField) }}</span><br>
                @endif
            </td>
        @else
        <td></td>
        @endif
    @endforeach
    @if($actions)
        <td></td>
    @endif
</tr>