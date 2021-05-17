@if(config('livewire-powergrid.filter') === 'inline')
    <tr class="border-b border-gray-200 hover:bg-gray-100">

        @if(count($make_filters))
            @if($checkbox)
                <td class="td_{{ $column->field }}"></td>
            @endif
            @foreach($columns as $column)
                @if($column->hidden === false)
                    <td class="td_{{ $column->field }}">

                        @if(isset($make_filters['date_picker']))
                            @foreach($make_filters['date_picker'] as $field => $date)
                                @if($date['field'] === $column->field)
                                    @include('livewire-powergrid::tailwind.2.components.date_picker', [
                                        'inline' => true,
                                        'class' => 'w-full'
                                    ])
                                @endif
                            @endforeach
                        @endif

                        @if(isset($make_filters['select']))
                            @foreach($make_filters['select'] as $field => $select)
                                @if($select['field'] === $column->field)
                                    @include('livewire-powergrid::tailwind.2.components.select', [
                                        'inline' => true
                                    ])
                                @endif
                            @endforeach
                        @endif

                        @if(isset($make_filters['multi_select']))
                            @foreach($make_filters['multi_select'] as $field => $multi_select)
                                @if($multi_select['field'] === $column->field)
                                    @include('livewire-powergrid::tailwind.2.components.multi_select', [
                                        'inline' => true
                                    ])
                                @endif
                            @endforeach
                        @endif

                        @if(isset($make_filters['number']))
                            @foreach($make_filters['number'] as $field => $number)
                                @if($number['field'] === $column->field)
                                    @include('livewire-powergrid::tailwind.2.components.number', [
                                        'inline' => true
                                    ])
                                @endif
                            @endforeach
                        @endif

                        @if(isset($make_filters['input_text']))
                            @foreach($make_filters['input_text'] as $field => $number)
                                @if($number['field'] === $column->field)
                                    @include('livewire-powergrid::tailwind.2.components.input_text', [
                                        'inline' => true
                                    ])
                                @endif
                            @endforeach
                        @endif

                        @if(isset($make_filters['boolean_filter']))
                            @foreach($make_filters['boolean_filter'] as $field => $boolean_filter)
                                @if($boolean_filter['field'] === $column->field)
                                    @include('livewire-powergrid::tailwind.2.components.boolean_filter', [
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
