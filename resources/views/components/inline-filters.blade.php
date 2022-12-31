@props([
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
@if(config('livewire-powergrid.filter') === 'inline')
    <tr class="{{ $theme->table->trClass }} {{ $theme->table->trFiltersClass }}"
        style="{{ $theme->table->trStyle }} {{ $theme->table->trFiltersStyle }}">

            @if(data_get($setUp, 'detail.showCollapseIcon'))
                <td class="{{ $theme->table->tdBodyClass }}" style="{{ $theme->table->tdBodyStyle }}"></td>
            @endif
            @if($checkbox)
                <td class="{{ $theme->table->tdBodyClass }}" style="{{ $theme->table->tdBodyStyle }}"></td>
            @endif

            @foreach($columns as $column)
                <td class="{{ $theme->table->tdBodyClass }}"
                    style="{{ $column->hidden === true ? 'display:none': '' }}; {{ $theme->table->tdBodyStyle }}">

                    @foreach($column->filters as $filter)
                        @if(str(data_get($filter, 'className'))->contains('FilterMultiSelect'))
                            <x-livewire-powergrid::inputs.select
                                :tableName="$tableName"
                                :filter="$filter"
                                :theme="$theme->filterMultiSelect"
                                :initialValues="data_get(data_get($filters, 'multi_select'), data_get($filter, 'field'), [])"/>
                        @endif

                        @if(str(data_get($filter, 'className'))->contains('FilterSelect'))
                                @includeIf($theme->filterSelect->view, [
                                   'inline' => true,
                                   'column' => $column,
                                   'filter' => $filter,
                                   'theme' => $theme->filterSelect,
                                ])
                        @endif
                        @if(str(data_get($filter, 'className'))->contains('FilterInputText'))
                                @includeIf($theme->filterInputText->view, [
                                   'inline'           => true,
                                   'enabledFilters'   => $enabledFilters,
                                   'filter'           => $filter,
                                   'theme'            => $theme->filterInputText,
                                ])
                        @endif
                            @if(str(data_get($filter, 'className'))->contains('FilterNumber'))
                                @includeIf($theme->filterNumber->view, [
                                   'inline'           => true,
                                   'enabledFilters'   => $enabledFilters,
                                   'filter'           => $filter,
                                   'theme'            => $theme->filterNumber,
                                ])
                            @endif
                    @endforeach


                        {{--                    @foreach(data_get($makeFilters, 'date_picker', []) as $index => $date)--}}
{{--                        @if(data_get($date, 'field') === $column->field)--}}
{{--                            @includeIf($theme->filterDatePicker->view, [--}}
{{--                                 'inline'    => true,--}}
{{--                                 'date'      => $date,--}}
{{--                                 'tableName' => $tableName,--}}
{{--                                 'classAttr' => 'w-full',--}}
{{--                                 'theme'     => $theme->filterDatePicker,--}}
{{--                            ])--}}
{{--                        @endif--}}
{{--                    @endforeach--}}

{{--                    @foreach(data_get($makeFilters, 'dynamic', []) as $index => $input)--}}
{{--                        @if(data_get($input, 'field') === $column->field)--}}
{{--                            @include(powerGridThemeRoot().'.filters.dynamic', [--}}
{{--                                'input' => $input,--}}
{{--                            ])--}}
{{--                        @endif--}}
{{--                    @endforeach--}}

{{--                    @endforeach--}}
{{--                    @foreach(data_get($makeFilters, 'boolean', []) as $index => $booleanFilter)--}}
{{--                        @if(data_get($booleanFilter, 'field') === $column->field)--}}
{{--                            @includeIf($theme->filterBoolean->view, [--}}
{{--                                'inline'         => true,--}}
{{--                                'booleanFilter'  => $booleanFilter,--}}
{{--                                'tableName'      => $tableName,--}}
{{--                                'theme'          => $theme->filterBoolean,--}}
{{--                           ])--}}
{{--                        @endif--}}
{{--                    @endforeach--}}
                </td>
            @endforeach
            @if(isset($actions) && count($actions))
                <td colspan="{{ count($actions) }}"></td>
            @endif
    </tr>
@endif
