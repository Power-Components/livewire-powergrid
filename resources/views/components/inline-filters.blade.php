@props([
    'checkbox' => null,
    'columns' => null,
    'actions' => null,
    'theme' => null,
    'enabledFilters' => null,
    'inputTextOptions' => [],
    'tableName' => null,
    'filters' => [],
    'setUp' => null,
])
@php
    $trClasses = Arr::toCssClasses([$theme->table->trClass, $theme->table->trFiltersClass]);
    $tdClasses = Arr::toCssClasses([$theme->table->tdBodyClass, $theme->table->tdFiltersClass]);
    $trStyles = Arr::toCssClasses([$theme->table->tdBodyStyle, $theme->table->trFiltersStyle]);
    $tdStyles = Arr::toCssClasses([$theme->table->tdBodyStyle, $theme->table->tdFiltersStyle]);
@endphp
@if (config('livewire-powergrid.filter') === 'inline')
    <tr
        class="{{ $trClasses }}"
        style="{{ $theme->table->trStyle }} {{ $theme->table->trFiltersStyle }}"
    >

        @if (data_get($setUp, 'detail.showCollapseIcon'))
            <td
                class="{{ $tdClasses }}"
                style="{{ $tdStyles }}"
            ></td>
        @endif
        @if ($checkbox)
            <td
                class="{{ $tdClasses }}"
                style="{{ $tdStyles }}"
            ></td>
        @endif

        @foreach ($this->visibleColumns as $column)
            <td
                class="{{ $theme->table->tdBodyClass }}"
                wire:key="column-filter-{{ $column->field }}"
                style="{{ $column->hidden === true ? 'display:none' : '' }}; {{ $theme->table->tdBodyStyle }}"
            >

                @foreach ($column->filters as $key => $filter)
                    <div wire:key="filter-{{ $column->field }}-{{ $key }}">
                        @if (str(data_get($filter, 'className'))->contains('FilterMultiSelect'))
                            <x-livewire-powergrid::inputs.select
                                :tableName="$tableName"
                                :filter="$filter"
                                :theme="$theme->filterMultiSelect"
                                :initialValues="data_get(data_get($filters, 'multi_select'), data_get($filter, 'field'), [])"
                            />
                        @endif
                        @if (str(data_get($filter, 'className'))->contains(['FilterSelect', 'FilterEnumSelect']))
                            @includeIf($theme->filterSelect->view, [
                                'inline' => true,
                                'column' => $column,
                                'filter' => $filter,
                                'theme' => $theme->filterSelect,
                            ])
                        @endif
                        @if (str(data_get($filter, 'className'))->contains('FilterInputText'))
                            @includeIf($theme->filterInputText->view, [
                                'inline' => true,
                                'filter' => $filter,
                                'theme' => $theme->filterInputText,
                            ])
                        @endif
                        @if (str(data_get($filter, 'className'))->contains('FilterNumber'))
                            @includeIf($theme->filterNumber->view, [
                                'inline' => true,
                                'filter' => $filter,
                                'theme' => $theme->filterNumber,
                            ])
                        @endif
                        @if (str(data_get($filter, 'className'))->contains('FilterDynamic'))
                            <x-dynamic-component
                                :component="data_get($filter, 'component', '')"
                                :attributes="new \Illuminate\View\ComponentAttributeBag(
                                    data_get($filter, 'attributes', []),
                                )"
                            />
                        @endif
                        @if (str(data_get($filter, 'className'))->contains('FilterDateTimePicker'))
                            @includeIf($theme->filterDatePicker->view, [
                                'inline' => true,
                                'filter' => $filter,
                                'type' => 'datetime',
                                'tableName' => $tableName,
                                'classAttr' => 'w-full',
                                'theme' => $theme->filterDatePicker,
                            ])
                        @endif
                        @if (str(data_get($filter, 'className'))->contains('FilterDatePicker'))
                            @includeIf($theme->filterDatePicker->view, [
                                'inline' => true,
                                'filter' => $filter,
                                'type' => 'date',
                                'tableName' => $tableName,
                                'classAttr' => 'w-full',
                                'theme' => $theme->filterDatePicker,
                            ])
                        @endif
                        @if (str(data_get($filter, 'className'))->contains('FilterBoolean'))
                            @includeIf($theme->filterBoolean->view, [
                                'inline' => true,
                                'filter' => $filter,
                                'theme' => $theme->filterBoolean,
                            ])
                        @endif
                    </div>
                @endforeach
            </td>
        @endforeach
        @if (isset($actions) && count($actions))
            <td colspan="{{ count($actions) }}"></td>
        @endif
    </tr>
@endif
