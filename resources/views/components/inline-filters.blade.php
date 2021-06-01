@props([
    'makeFilters' => null,
    'checkbox' => null,
    'columns' => null,
    'columns' => null,
    'theme' => null
])
<div>
    @if(config('livewire-powergrid.filter') === 'inline')
        <tr class="{{ $theme->table->trClass }}">

            @if(count($makeFilters))
                @if($checkbox)
                    <td class="{{ $theme->table->tdBodyClass }}"></td>
                @endif
                @foreach($columns as $column)
                    @if($column->hidden === false)
                        <td>
                            @if(isset($makeFilters['date_picker']))
                                @foreach($makeFilters['date_picker'] as $field => $date)
                                    @if($date['field'] === $column->field)
                                        <x-livewire-powergrid::filters.date-picker
                                            :date="$date"
                                            :inline="true"
                                            :theme="$theme->filterDatePicker"/>
                                    @endif
                                @endforeach
                            @endif

                            @if(isset($makeFilters['select']))
                                @foreach($makeFilters['select'] as $field => $select)
                                    @if($select['field'] === $column->field)
                                        <x-livewire-powergrid::filters.select
                                            :select="$select"
                                            :inline="true"
                                            :theme="$theme->filterSelect"/>
                                    @endif
                                @endforeach
                            @endif

                            @if(isset($makeFilters['multi_select']))
                                @foreach($makeFilters['multi_select'] as $field => $multiSelect)
                                    @if($multiSelect['field'] === $column->field)
                                        <x-livewire-powergrid::filters.multi-select
                                            :multiSelect="$multiSelect"
                                            :column="$column"
                                            :inline="true"
                                            :theme="$theme->filterMultiSelect"/>
                                    @endif
                                @endforeach
                            @endif

                            @if(isset($makeFilters['number']))
                                @foreach($makeFilters['number'] as $field => $number)
                                    @if($number['field'] === $column->field)
                                        <x-livewire-powergrid::filters.number
                                            :number="$number"
                                            :inline="true"
                                            :theme="$theme->filterNumber"/>
                                    @endif
                                @endforeach
                            @endif

                            @if(isset($makeFilters['input_text']))
                                @foreach($makeFilters['input_text'] as $field => $inputText)
                                    @if($inputText['field'] === $column->field)
                                        <x-livewire-powergrid::filters.input-text
                                            :inputText="$inputText"
                                            :column="$column"
                                            :inline="true"
                                            :theme="$theme->filterInputText"/>
                                    @endif
                                @endforeach
                            @endif

                            @if(isset($makeFilters['boolean_filter']))
                                @foreach($makeFilters['boolean_filter'] as $field => $booleanFilter)
                                    @if($booleanFilter['field'] === $column->field)
                                        <x-livewire-powergrid::filters.boolean-filter
                                            :booleanFilter="$booleanFilter"
                                            :inline="true"
                                            :theme="$theme->filterBoolean"/>
                                    @endif
                                @endforeach
                            @endif

                        </td>
                    @endif
                @endforeach
                @if(isset($actions) && count($actions))
                    <td colspan="{{count($actions)}}"></td>
                @endif
            @endif
        </tr>
    @endif
</div>
