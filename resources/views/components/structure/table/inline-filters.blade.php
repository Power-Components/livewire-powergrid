@props([
    'checkbox' => null,
    'columns' => null,
    'actions' => null,
    'enabledFilters' => null,
    'inputTextOptions' => [],
    'tableName' => null,
    'filters' => [],
    'setUp' => null,
])

@php
    $trClasses = Arr::toCssClasses([theme('table.layout.tr'), theme('table.layout.body.tr.filters')]);
    $tdClasses = Arr::toCssClasses([theme('table.layout.td'), theme('table.layout.body.td.filters')]);
@endphp
@if (config('livewire-powergrid.filter') === 'inline')
    <tr
        class="{{ $trClasses }}"
    >

        @if (data_get($setUp, 'detail.showCollapseIcon'))
            <td
                class="{{ $tdClasses }}"
            ></td>
        @endif
        @if ($checkbox)
            <td
                class="{{ $tdClasses }}"
            ></td>
        @endif

        @foreach ($columns as $column)
            @php
                $filterClass = str(data_get($column, 'filters.className'));
            @endphp
            <td
                @class([
                    theme('table.layout.td'),
                    theme('table.layout.body.td.filters'),
                ])
                wire:key="column-filter-{{ data_get($column, 'field') }}"
                @style([
                    'display:none' => data_get($column, 'hidden') === true,
                ])
            >
                <div wire:key="filter-{{ data_get($column, 'field') }}-{{ $loop->index }}">
                    @if ($filterClass->contains('FilterMultiSelect'))
                        <x-livewire-powergrid::inputs.select
                            :table-name="$tableName"
                            :title="data_get($column, 'title')"
                            :filter="(array) data_get($column, 'filters')"
                            :initial-values="data_get($filters, 'multi_select.' . data_get($column, 'filters.field'))"
                        />
                    @elseif ($filterClass->contains(['FilterSelect', 'FilterEnumSelect']))
                        @includeIf(theme_view('filter.select'), [
                            'inline' => true,
                            'filter' => (array) data_get($column, 'filters'),
                        ])
                    @elseif ($filterClass->contains('FilterInputText'))
                        @includeIf(theme_view('filter.input_text'), [
                            'inline' => true,
                            'filter' => (array) data_get($column, 'filters'),
                        ])
                    @elseif ($filterClass->contains('FilterNumber'))
                        @includeIf(theme_view('filter.number'), [
                            'inline' => true,
                            'filter' => (array) data_get($column, 'filters'),
                        ])
                    @elseif ($filterClass->contains('FilterDateTimePicker'))
                        @includeIf(theme_view('filter.date_picker'), [
                            'inline' => true,
                            'filter' => (array) data_get($column, 'filters'),
                            'type' => 'datetime',
                            'tableName' => $tableName,
                            'classAttr' => 'w-full',
                        ])
                    @elseif ($filterClass->contains('FilterDatePicker'))
                        @includeIf(theme_view('filter.date_picker'), [
                            'inline' => true,
                            'filter' => (array) data_get($column, 'filters'),
                            'type' => 'date',
                            'tableName' => $tableName,
                            'classAttr' => 'w-full',
                        ])
                    @elseif ($filterClass->contains('FilterBoolean'))
                        @includeIf(theme_view('filter.boolean'), [
                            'inline' => true,
                            'filter' => (array) data_get($column, 'filters'),
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
