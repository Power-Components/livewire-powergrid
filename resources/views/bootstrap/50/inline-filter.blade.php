@if(config('livewire-powergrid.filter') === 'inline')
    <tr class="border-b border-gray-200 hover:bg-gray-100 ">

        @if(count($make_filters))
            @if($checkbox)
                <td></td>
            @endif
            @foreach($columns as $column)
                @if($column->hidden === false)
                    <td>
                        @if(isset($make_filters['date_picker']))
                            @foreach($make_filters['date_picker'] as $field => $date)
                                @if($date['field'] === $column->field)
                                    @include('livewire-powergrid::bootstrap.50.components.date_picker', [
                                        'date' => $date,
                                        'inline' => true
                                    ])
                                @endif
                            @endforeach
                        @endif
                        @if(isset($make_filters['select']))
                            @foreach($make_filters['select'] as $field => $select)
                                @if($select['field'] === $column->field)
                                    @include('livewire-powergrid::bootstrap.50.components.select', [
                                        'select' => $select,
                                        'inline' => true
                                    ])
                                @endif
                            @endforeach
                        @endif

                    </td>
                @endif
            @endforeach
            @if(isset($actionBtns) && count($actionBtns))
                <td colspan="{{count($actionBtns)}}"></td>
            @endif
        @endif
    </tr>

@endif
