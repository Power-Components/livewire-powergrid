@props([
'theme' => null,
'data' => null,
'actions' => null,
'checkbox' => null,
'primaryKey' => null,
'columns' => null,
'currentTable' => null,
])

<tr class="{{ $theme->table->trBodyClass }} font-bold" style="{{ $theme->table->trBodyStyle }}">
    @if($checkbox)
        <td></td>
    @endif
    @foreach ($columns as $column)
        @if($column->hidden === false)
            @if ($column->hasSum && $column->sum['footer'])
                <td class="{{ $theme->table->tdBodyClass . ' '.$column->bodyClass ?? '' }}"
                    style=" {{ $theme->table->tdBodyStyle . ' '.$column->bodyStyle ?? '' }}">
                        <span class="">{{ $column->sum['label'] }}: {{ $data->collect()->sum($column->field) }}</span>
                </td>
            @elseif ($column->hasCount && $column->count['footer'])
                <td class="{{ $theme->table->tdBodyClass . ' '.$column->bodyClass ?? '' }}"
                    style=" {{ $theme->table->tdBodyStyle . ' '.$column->bodyStyle ?? '' }}">
                        <span class="">{{ $column->count['label'] }}: {{ $data->collect()->count($column->field) }}</span>
                </td>
            @elseif ($column->hasAvg && $column->avg['footer'])
                <td class="{{ $theme->table->tdBodyClass . ' '.$column->bodyClass ?? '' }}"
                    style=" {{ $theme->table->tdBodyStyle . ' '.$column->bodyStyle ?? '' }}">
                        <span class="">{{ $column->avg['label'] }}: {{ $data->collect()->avg($column->field) }}</span>
                </td>
            @else
                <td></td>           
            @endif
        @endif
    @endforeach
    @if($actions)
        <td></td>
    @endif
</tr>