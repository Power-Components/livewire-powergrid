@props([
    'checkbox' => null,
    'columns' => null,
    'actions' => null,
    'enabledFilters' => null,
    'inputTextOptions' => [],
    'tableName' => null,
    'filters' => [],
    'setUp' => null,
    '__partial' => null,
])

@php
    $__partial = $__partial ?? $this;
    $checkbox = $checkbox ?? $__partial->checkbox;
    $columns = $columns ?? $__partial->columns;
    $tableName = $tableName ?? $__partial->tableName;
    $filters = $filters ?? $__partial->filters;
    $setUp = $setUp ?? $__partial->setUp;
    $trClasses = Arr::toCssClasses([theme('table.layout.tr'), theme('table.layout.body.tr.filters')]);
    $tdClasses = Arr::toCssClasses([theme('table.layout.td'), theme('table.layout.body.td.filters')]);
@endphp
@if ($__partial->usesFilterInline())
    <tr
        class="{{ $trClasses }}"
        wire:key="pg-inline-filters-{{ $tableName }}"
        wire:partial.ignore="pg-inline-filters-{{ $tableName }}"
        data-pg-inline-filters
    >

        @if (data_get($setUp, 'detail.showCollapseIcon'))
            <td
                class="{{ $tdClasses }}"
            ></td>
        @endif
        @isset($setUp['responsive'])
            <td
                class="{{ $tdClasses }}"
            ></td>
        @endisset
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
                            :__partial="$__partial"
                        />
                    @elseif ($filterClass->contains(['FilterSelect', 'FilterEnumSelect']))
                        @includeIf(theme_view('filter.select'), [
                            'inline' => true,
                            'filter' => (array) data_get($column, 'filters'),
                            '__partial' => $__partial,
                        ])
                    @elseif ($filterClass->contains('FilterInputText'))
                        @includeIf(theme_view('filter.input_text'), [
                            'inline' => true,
                            'filter' => (array) data_get($column, 'filters'),
                            '__partial' => $__partial,
                        ])
                    @elseif ($filterClass->contains('FilterNumber'))
                        @includeIf(theme_view('filter.number'), [
                            'inline' => true,
                            'filter' => (array) data_get($column, 'filters'),
                            '__partial' => $__partial,
                        ])
                    @elseif ($filterClass->contains('FilterDateTimePicker'))
                        @includeIf(theme_view('filter.date_picker'), [
                            'inline' => true,
                            'filter' => (array) data_get($column, 'filters'),
                            'type' => 'datetime',
                            'tableName' => $tableName,
                            'classAttr' => 'w-full',
                            '__partial' => $__partial,
                        ])
                    @elseif ($filterClass->contains('FilterDatePicker'))
                        @includeIf(theme_view('filter.date_picker'), [
                            'inline' => true,
                            'filter' => (array) data_get($column, 'filters'),
                            'type' => 'date',
                            'tableName' => $tableName,
                            'classAttr' => 'w-full',
                            '__partial' => $__partial,
                        ])
                    @elseif ($filterClass->contains('FilterBoolean'))
                        @includeIf(theme_view('filter.boolean'), [
                            'inline' => true,
                            'filter' => (array) data_get($column, 'filters'),
                            '__partial' => $__partial,
                        ])
                    @elseif ($filterClass->contains('FilterDynamic'))
                        <x-dynamic-component
                            :component="data_get($column, 'filters.component')"
                            :attributes="new \Illuminate\View\ComponentAttributeBag(
                                array_merge(
                                    data_get($column, 'filters.attributes', []),
                                    ['__partial' => $__partial]
                                ),
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
