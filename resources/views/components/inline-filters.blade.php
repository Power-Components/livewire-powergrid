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
            @php
                $filterClass = str(data_get($column, 'filters.className'));
            @endphp
            <td
                @class([data_get($theme, 'table.tdBodyClass'), data_get($theme, 'table.tdFiltersClass')])
                wire:key="column-filter-{{ data_get($column, 'field') }}"
                @style([
                    'display:none' => data_get($column, 'hidden') === true,
                    data_get($theme, 'table.tdBodyStyle'),
                    data_get($theme, 'table.tdFiltersStyle')
                ])
            >
                <div wire:key="filter-{{ data_get($column, 'field') }}-{{ $loop->index }}">
                    @if ($filterClass->contains('FilterMultiSelect'))
                        <x-livewire-powergrid::inputs.select
                            :table-name="$tableName"
                            :title="data_get($column, 'title')"
                            :filter="(array) data_get($column, 'filters')"
                            :theme="data_get($theme, 'filterMultiSelect')"
                            :initial-values="data_get($filters, 'multi_select.'.data_get($column, 'dataField'))"
                        />
                    @elseif ($filterClass->contains(['FilterSelect', 'FilterEnumSelect']))
                        @includeIf(data_get($theme, 'filterSelect.view'), [
                            'inline' => true,
                            'filter' => (array) data_get($column, 'filters'),
                            'theme' => data_get($theme, 'filterSelect'),
                        ])
                    @elseif ($filterClass->contains('FilterInputText'))
                        @includeIf(data_get($theme, 'filterInputText.view'), [
                            'inline' => true,
                            'filter' => (array) data_get($column, 'filters'),
                            'theme' => data_get($theme, 'filterInputText'),
                        ])
                    @elseif ($filterClass->contains('FilterNumber'))
                        @includeIf(data_get($theme, 'filterNumber.view'), [
                            'inline' => true,
                            'filter' => (array) data_get($column, 'filters'),
                            'theme' => data_get($theme, 'filterNumber'),
                        ])
                    @elseif ($filterClass->contains('FilterDateTimePicker'))
                        @includeIf(data_get($theme, 'filterDatePicker.view'), [
                            'inline' => true,
                            'filter' => (array) data_get($column, 'filters'),
                            'type' => 'datetime',
                            'tableName' => $tableName,
                            'classAttr' => 'w-full',
                            'theme' => data_get($theme, 'filterDatePicker'),
                        ])
                    @elseif ($filterClass->contains('FilterDatePicker'))
                        @includeIf(data_get($theme, 'filterDatePicker.view'), [
                            'inline' => true,
                            'filter' => (array) data_get($column, 'filters'),
                            'type' => 'date',
                            'classAttr' => 'w-full',
                            'theme' => data_get($theme, 'filterDatePicker'),
                        ])
                    @elseif ($filterClass->contains('FilterBoolean'))
                        @includeIf(data_get($theme, 'filterBoolean.view'), [
                            'inline' => true,
                            'filter' => (array) data_get($column, 'filters'),
                            'theme' => data_get($theme, 'filterBoolean'),
                        ])
                    @elseif ($filterClass->contains('FilterDynamic'))
                        <x-dynamic-component
                            :component="data_get($column, 'filters.component')"
                            :attributes="new \Illuminate\View\ComponentAttributeBag(
                                data_get($column, 'filters.attributes', []),
                            )"
                        />
                    @endif
                </div>
            </td>
        @endforeach
        @if (isset($actions) && count($actions))
            <td colspan="{{ count($actions) }}"></td>
        @endif
    </tr>
@endif
