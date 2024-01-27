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
    $trClasses = Arr::toCssClasses([data_get($theme, 'table.trClass'), data_get($theme, 'table.trFiltersClass')]);
    $tdClasses = Arr::toCssClasses([data_get($theme, 'table.tdBodyClass'), data_get($theme, 'table.tdFiltersClass')]);
    $trStyles = Arr::toCssClasses([data_get($theme, 'table.trBodyStyle'), data_get($theme, 'table.trFiltersStyle')]);
    $tdStyles = Arr::toCssClasses([data_get($theme, 'table.tdBodyStyle'), data_get($theme, 'table.tdFiltersStyle')]);
@endphp
@if (config('livewire-powergrid.filter') === 'inline')
    <tr
        class="{{ $trClasses }}"
        style="{{ data_get($theme, 'table.trStyle') }} {{ data_get($theme, 'table.trFiltersStyle') }}"
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
                class="{{ data_get($theme, 'table.tdBodyClass') }}"
                wire:key="column-filter-{{ data_get($column, 'field') }}"
                style="{{ data_get($column, 'hidden') === true ? 'display:none' : '' }}; {{ data_get($theme, 'table.tdBodyStyle') }}"
            >
                <div wire:key="filter-{{ data_get($column, 'field') }}-{{ $loop->index }}">
                    @php
                        $filterClass = str(data_get($column, 'filters.className'));
                    @endphp

                    @if ($filterClass->contains('FilterMultiSelect'))
                        <x-livewire-powergrid::inputs.select
                                :tableName="$tableName"
                                :filter="(array) data_get($column, 'filters')"
                                :theme="data_get($theme, 'filterMultiSelect')"
                                :initialValues="data_get(data_get($column, 'filters.multi_select'), data_get($column, 'filters.field'), [])"
                        />
                    @endif

                    @if ($filterClass->contains(['FilterSelect', 'FilterEnumSelect']))
                        @includeIf(data_get($theme, 'filterSelect.view'), [
                            'inline' => true,
                            'column' => $column,
                            'filter' => (array) data_get($column, 'filters'),
                            'theme' => data_get($theme, 'filterSelect'),
                        ])
                    @endif

                    @if ($filterClass->contains('FilterInputText'))
                        @includeIf(data_get($theme, 'filterInputText.view'), [
                            'inline' => true,
                            'filter' => (array) data_get($column, 'filters'),
                            'theme' => data_get($theme, 'filterInputText'),
                        ])
                    @endif

                    @if ($filterClass->contains('FilterNumber'))
                        @includeIf(data_get($theme, 'filterNumber.view'), [
                            'inline' => true,
                            'filter' => (array) data_get($column, 'filters'),
                            'theme' => data_get($theme, 'filterNumber'),
                        ])
                    @endif

                    @if ($filterClass->contains('FilterDateTimePicker'))
                        @includeIf(data_get($theme, 'filterDatePicker.view'), [
                            'inline' => true,
                            'filter' => (array) data_get($column, 'filters'),
                            'type' => 'datetime',
                            'tableName' => $tableName,
                            'classAttr' => 'w-full',
                            'theme' => data_get($theme, 'filterDatePicker'),
                        ])
                    @endif

                    @if ($filterClass->contains('FilterDatePicker'))
                        @includeIf(data_get($theme, 'filterDatePicker.view'), [
                            'inline' => true,
                            'filter' => (array) data_get($column, 'filters'),
                            'type' => 'date',
                            'tableName' => $tableName,
                            'classAttr' => 'w-full',
                            'theme' => data_get($theme, 'filterDatePicker'),
                        ])
                    @endif

                    @if ($filterClass->contains('FilterBoolean'))
                        @includeIf(data_get($theme, 'filterBoolean.view'), [
                            'inline' => true,
                            'filter' => (array) data_get($column, 'filters'),
                            'theme' => data_get($theme, 'filterBoolean'),
                        ])
                    @endif


                    {{--                    @if (str(data_get($filter, 'className'))->contains('FilterDynamic'))--}}
                    {{--                        <x-dynamic-component--}}
                    {{--                                :component="data_get($filter, 'component', '')"--}}
                    {{--                                :attributes="new \Illuminate\View\ComponentAttributeBag(--}}
                    {{--                                    data_get($filter, 'attributes', []),--}}
                    {{--                                )"--}}
                    {{--                        />--}}
                    {{--                    @endif--}}
                </div>
            </td>
        @endforeach
        @if (isset($actions) && count($actions))
            <td colspan="{{ count($actions) }}"></td>
        @endif
    </tr>
@endif
