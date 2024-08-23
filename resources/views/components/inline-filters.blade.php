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
    $trClasses = Arr::toCssClasses([theme_style($theme, 'table.body.tr'), theme_style($theme, 'table.body.trFilters')]);
    $tdClasses = Arr::toCssClasses([theme_style($theme, 'table.body.td'), theme_style($theme, 'table.body.tdFilters')]);
    $trStyles = Arr::toCssClasses([
        theme_style($theme, 'table.body.tr.1'),
        theme_style($theme, 'table.body.trFilters.1'),
    ]);
    $tdStyles = Arr::toCssClasses([
        theme_style($theme, 'table.body.td.1'),
        theme_style($theme, 'table.body.tdFilters.1'),
    ]);
@endphp
@if (config('livewire-powergrid.filter') === 'inline')
    <tr
        class="{{ $trClasses }}"
        style="{{ $trStyles }}"
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

        @foreach (collect($columns)->map(function ($column) {
        data_forget($column, 'rawQueries');

        return $column;
    }) as $column)
            @php
                $filterClass = str(data_get($column, 'filters.className'));
            @endphp
            <td
                @class([
                    theme_style($theme, 'table.body.td'),
                    theme_style($theme, 'table.body.tdFilters'),
                ])
                wire:key="column-filter-{{ data_get($column, 'field') }}"
                @style([
                    'display:none' => data_get($column, 'hidden') === true,
                    theme_style($theme, 'table.body.td.1'),
                    theme_style($theme, 'table.body.tdFilters.1'),
                ])
            >
                <div wire:key="filter-{{ data_get($column, 'field') }}-{{ $loop->index }}">
                    @if ($filterClass->contains('FilterMultiSelect'))
                        <x-livewire-powergrid::inputs.select
                            :table-name="$tableName"
                            :theme="$theme"
                            :title="data_get($column, 'title')"
                            :filter="(array) data_get($column, 'filters')"
                            :initial-values="data_get($filters, 'multi_select.' . data_get($column, 'dataField'))"
                        />
                    @elseif ($filterClass->contains(['FilterSelect', 'FilterEnumSelect']))
                        @includeIf(theme_style($theme, 'filterSelect.view'), [
                            'inline' => true,
                            'filter' => (array) data_get($column, 'filters'),
                        ])
                    @elseif ($filterClass->contains('FilterInputText'))
                        @includeIf(theme_style($theme, 'filterInputText.view'), [
                            'inline' => true,
                            'filter' => (array) data_get($column, 'filters'),
                        ])
                    @elseif ($filterClass->contains('FilterNumber'))
                        @includeIf(theme_style($theme, 'filterNumber.view'), [
                            'inline' => true,
                            'filter' => (array) data_get($column, 'filters'),
                        ])
                    @elseif ($filterClass->contains('FilterDateTimePicker'))
                        @includeIf(theme_style($theme, 'filterDatePicker.view'), [
                            'inline' => true,
                            'filter' => (array) data_get($column, 'filters'),
                            'type' => 'datetime',
                            'tableName' => $tableName,
                            'classAttr' => 'w-full',
                        ])
                    @elseif ($filterClass->contains('FilterDatePicker'))
                        @includeIf(theme_style($theme, 'filterDatePicker.view'), [
                            'inline' => true,
                            'filter' => (array) data_get($column, 'filters'),
                            'type' => 'date',
                            'classAttr' => 'w-full',
                        ])
                    @elseif ($filterClass->contains('FilterBoolean'))
                        @includeIf(theme_style($theme, 'filterBoolean.view'), [
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
