@props([
    'loading' => false,
    '__partial' => null,
])

@php
    $__partial = $__partial ?? $this;
    $setUp = $__partial->setUp;
    $tableName = $__partial->tableName;
    $columns = $__partial->columns;
    $enabledFilters = $__partial->enabledFilters;
    $radio = $__partial->radio;
    $checkbox = $__partial->checkbox;
    $actions = $__partial->actions ?? [];
@endphp
<tr
    class="{{ theme('table.layout.tr') }}"
>
    @if ($loading)
        <td
            class="{{ theme('table.layout.td') }}"
            colspan="999"
        >
            @if ($__partial->loadingComponent)
                @include($__partial->loadingComponent)
            @else
                {{ __('Loading') }}
            @endif
        </td>
    @else
        @if (data_get($setUp, 'detail.showCollapseIcon'))
            <th
                scope="col"
                class="{{ theme('table.layout.th') }}"
                wire:key="show-collapse-{{ $tableName }}"
            >
            </th>
        @endif

        @isset($setUp['responsive'])
            <th
                fixed
                x-show="hasHiddenElements"
                class="{{ theme('table.layout.th') }}"
            >
            </th>
        @endisset

        @if ($radio)
            <th
                class="{{ theme('table.layout.th') }}"
            >
            </th>
        @endif

        @if ($checkbox)
            @include(theme_view('table.checkbox-all'), ['__partial' => $__partial])
        @endif

        @foreach ($columns as $column)
            @include(theme_view('table.cols'), [
                'column'         => $column,
                'enabledFilters' => $enabledFilters,
                'setUp'          => $setUp,
                'tableName'      => $tableName,
                'multiSort'      => $__partial->multiSort,
                'sortArray'      => $__partial->sortArray,
                'sortField'      => $__partial->sortField,
                'sortDirection'  => $__partial->sortDirection,
                '__partial'      => $__partial,
            ])
        @endforeach

        @if (isset($actions) && count($actions))
            @php
                $responsiveActionsColumnName =
                    \PowerComponents\Turbine\Components\SetUp\Responsive::ACTIONS_COLUMN_NAME;

                $isActionFixedOnResponsive =
                    isset($setUp['responsive']) &&
                    in_array($responsiveActionsColumnName, data_get($setUp, 'responsive.fixedColumns'))
                        ? true
                        : false;
            @endphp

            <th
                @if ($isActionFixedOnResponsive) fixed @endif
                class="{{ theme('table.layout.th_actions') }}"
                scope="col"
                colspan="999"
                wire:key="{{ md5('actions') }}"
            >
                {{ trans('livewire-powergrid::datatable.labels.action') }}
            </th>
        @endif
    @endif
</tr>
