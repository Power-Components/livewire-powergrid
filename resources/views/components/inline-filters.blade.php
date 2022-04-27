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
    'setUp' => null
])
<div>
    @if(config('livewire-powergrid.filter') === 'inline')
        <tr class="{{ $theme->table->trClass }} {{ $theme->table->trFiltersClass }}"
            style="{{ $theme->table->trStyle }} {{ $theme->table->trFiltersStyle }}">
            @if(count($makeFilters))
                @if(data_get($setUp, 'detail.showCollapseIcon'))
                    <td class="{{ $theme->table->tdBodyClass }}" style="{{ $theme->table->tdBodyStyle }}"></td>
                @endif
                @if($checkbox)
                    <td class="{{ $theme->table->tdBodyClass }}" style="{{ $theme->table->tdBodyStyle }}"></td>
                @endif
                @foreach($columns as $column)
                    <td class="{{ $theme->table->tdBodyClass }}"
                        style="{{ $column->hidden === true ? 'display:none': '' }}; {{ $theme->table->tdBodyStyle }}">
                        @foreach(data_get($makeFilters, 'date_picker', []) as $index => $date)
                            @if(data_get($date, 'field') === $column->field)
                                @includeIf($theme->filterDatePicker->view, [
                                     'inline'    => true,
                                     'date'      => $date,
                                     'tableName' => $tableName,
                                     'classAttr' => 'w-full',
                                     'theme'     => $theme->filterDatePicker,
                                ])
                            @endif
                        @endforeach
                        @foreach(data_get($makeFilters, 'select', []) as $index => $select)
                            @if(data_get($select, 'field') === $column->field)
                                @includeIf($theme->filterSelect->view, [
                                    'inline' => true,
                                    'column' => $column,
                                    'select' =>$select,
                                    'theme' => $theme->filterSelect,
                                ])
                            @endif
                        @endforeach
                        @foreach(data_get($makeFilters, 'multi_select', []) as $index => $multiSelect)
                            @if(data_get($multiSelect, 'field') === $column->field)
                                @includeIf($theme->filterMultiSelect->view, [
                                    'inline'    => true,
                                    'column'    => $column,
                                    'selected'  => $filters['multi_select'] ?? [],
                                    'tableName' => $tableName,
                                    'theme'     => $theme->filterMultiSelect,
                                ])
                            @endif
                        @endforeach
                        @foreach(data_get($makeFilters, 'number', []) as $index => $number)
                            @if(data_get($number, 'field') === $column->field)
                                @includeIf($theme->filterNumber->view, [
                                     'inline' => true,
                                     'column' => $column,
                                     'number' => $number,
                                     'theme'  => $theme->filterNumber,
                                ])
                            @endif
                        @endforeach
                        @foreach(data_get($makeFilters, 'input_text', []) as $index => $inputText)
                            @if(data_get($inputText, 'field') === $column->field)
                                @includeIf($theme->filterInputText->view, [
                                     'inline'           => true,
                                     'enabledFilters'   => $enabledFilters,
                                     'inputTextOptions' => $inputTextOptions,
                                     'enabledFilters'   => $enabledFilters,
                                     'theme'            => $theme->filterInputText,
                                ])
                            @endif
                        @endforeach
                        @foreach(data_get($makeFilters, 'boolean', []) as $index => $booleanFilter)
                            @if(data_get($booleanFilter, 'field') === $column->field)
                                @includeIf($theme->filterBoolean->view, [
                                    'inline'         => true,
                                    'booleanFilter'  => $booleanFilter,
                                    'tableName'      => $tableName,
                                    'theme'          => $theme->filterBoolean,
                               ])
                            @endif
                        @endforeach
                    </td>
                @endforeach
                @if(isset($actions) && count($actions))
                    <td colspan="{{ count($actions) }}"></td>
                @endif
            @endif
        </tr>
    @endif
</div>
