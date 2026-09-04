@props([
    'loading' => false,
])
<tr
    class="{{ theme('table.layout.tr') }}"
>
    @if ($loading)
        <td
            class="{{ theme('table.layout.empty_state') }}"
            colspan="999"
        >
            @if ($loadingComponent)
                @include($loadingComponent)
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
            @include(theme_view('table.checkbox-all'))
        @endif

        @include(theme_view('table.cols'), [
            'columns' => $columns,
            'enabledFilters' => $enabledFilters,
            'multiSort' => $this->multiSort,
            'sortArray' => $this->sortArray,
            'sortField' => $this->sortField,
            'sortDirection' => $this->sortDirection,
            'tableName' => $tableName,
        ])

        @if (isset($actions) && count($actions))
            @php
                $responsiveActionsColumnName =
                    \PowerComponents\Turbine\Components\SetUp\Responsive::ACTIONS_COLUMN_NAME;

                $isActionFixedOnResponsive =
                    isset($this->setUp['responsive']) &&
                    in_array($responsiveActionsColumnName, data_get($this->setUp, 'responsive.fixedColumns'))
                        ? true
                        : false;
            @endphp

            <th
                @if ($isActionFixedOnResponsive) fixed @endif
                class="{{ theme('table.layout.th') . ' ' . theme('table.header.th_action') }}"
                scope="col"
                colspan="999"
                wire:key="{{ md5('actions') }}"
            >
                {{ trans('livewire-powergrid::datatable.labels.action') }}
            </th>
        @endif
    @endif
</tr>
