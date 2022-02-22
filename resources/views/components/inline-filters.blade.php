@props([
    'makeFilters' => null,
    'checkbox' => null,
    'columns' => null,
    'actions' => null,
    'theme' => null,
    'enabledFilters' => null,
    'inputTextOptions' => [],
    'tableName' => null,
    'filters' => [],
])
<div>
    @if(config('livewire-powergrid.filter') === 'inline')
        <tr class="{{ $theme->table->trClass }}" class="{{ $theme->table->trStyle }}">

            @if(count($makeFilters))
                @if($checkbox)
                    <td class="{{ $theme->table->tdBodyClass }}" style="{{ $theme->table->tdBodyStyle }}"></td>
                @endif

                @foreach($columns as $column)
                    <td class="{{ $theme->table->tdBodyClass }}"
                        style="{{ $column->hidden === true ? 'display:none': '' }}; {{ $theme->table->tdBodyStyle }}">
                        @foreach(data_get($makeFilters, 'date_picker', []) as $index => $date)
                            @if(data_get($date, 'field') === $column->field)
                                <x-livewire-powergrid::filters.date-picker
                                    :date="$date"
                                    :inline="true"
                                    :column="$column"
                                    :tableName="$tableName"
                                    :theme="$theme->filterDatePicker"/>
                            @endif
                        @endforeach

                        @foreach(data_get($makeFilters, 'select', []) as $index => $select)
                            @if(data_get($select, 'field') === $column->field)
                                <x-livewire-powergrid::filters.select
                                    :column="$column"
                                    :select="$select"
                                    :inline="true"
                                    :theme="$theme->filterSelect"/>
                            @endif
                        @endforeach

                        @foreach(data_get($makeFilters, 'multi_select', []) as $index => $multiSelect)
                            @if(data_get($multiSelect, 'field') === $column->field)
                                @includeIf($theme->filterMultiSelect->view, [
                                        'inline' => true,
                                        'column' => $column,
                                        'selected' => $filters['multi_select'] ?? [],
                                        'tableName' => $tableName,
                                ])
                            @endif
                        @endforeach

                        @foreach(data_get($makeFilters, 'number', []) as $index => $number)
                            @if(data_get($number, 'field') === $column->field)
                                <x-livewire-powergrid::filters.number
                                    :number="$number"
                                    :column="$column"
                                    :inline="true"
                                    :theme="$theme->filterNumber"/>
                            @endif
                        @endforeach

                        @foreach(data_get($makeFilters, 'input_text', []) as $index => $inputText)
                            @if(data_get($inputText, 'field') === $column->field)
                                <x-livewire-powergrid::filters.input-text
                                    :inputText="$inputText"
                                    :enabledFilters="$enabledFilters"
                                    :inputTextOptions="$inputTextOptions"
                                    :column="$column"
                                    :inline="true"
                                    :theme="$theme->filterInputText"/>
                            @endif
                        @endforeach

                        @foreach(data_get($makeFilters, 'boolean_filter', []) as $index => $booleanFilter)
                            @if(data_get($booleanFilter, 'field') === $column->field)
                                <x-livewire-powergrid::filters.boolean-filter
                                    :column="$column"
                                    :booleanFilter="$booleanFilter"
                                    :inline="true"
                                    :theme="$theme->filterBoolean"/>
                            @endif
                        @endforeach
                    </td>
                @endforeach
                @if(isset($actions) && count($actions))
                    <td colspan="{{count($actions)}}"></td>
                @endif
            @endif
        </tr>
    @endif
</div>
