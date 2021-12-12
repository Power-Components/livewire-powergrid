@props([
'theme' => null,
'data' => null,
'actions' => null,
'totalRow' => null,
'checkbox' => null,
'primaryKey' => null,
'columns' => null,
'currentTable' => null,
])

@if ($totalRow)
<tr class="{{ $theme->table->trBodyClass }} font-bold" style="{{ $theme->table->trBodyStyle }}">
    @if($checkbox)
        <td class="{{ $theme->table->tdBodyClass }}"
            style=" {{ $theme->table->tdBodyStyle }}">
            Total
        </td>
    @endif
    @foreach ($columns as $column)
        @if($column->hidden === false)
            @if ($column->totalRow)
                <td class="{{ $theme->table->tdBodyClass . ' '.$column->bodyClass ?? '' }}"
                    style=" {{ $theme->table->tdBodyStyle . ' '.$column->bodyStyle ?? '' }}">
                    <span class="">{{ $data->collect()->sum($column->field) }}</span>
                </td>
            @else
                <td></td>
            @endif
        @endif
    @endforeach
    @if($actions)
        <td colspan="{{ count($actions) }}">Total</td>
    @endif
</tr>
@endif