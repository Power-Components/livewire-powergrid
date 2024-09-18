@props([
    'loading' => false,
])
<tr
    class="{{ theme_style($theme, 'table.header.tr') }}"
>
    @if ($loading)
        <td
            class="{{ theme_style($theme, 'table.body.tbodyEmpty') }}"
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
                class="{{ theme_style($theme, 'table.header.th') }}"
                wire:key="show-collapse-{{ $tableName }}"
            >
            </th>
        @endif

        @isset($setUp['responsive'])
            <th
                fixed
                x-show="hasHiddenElements"
                class="{{ theme_style($theme, 'table.header.th') }}"
            >
            </th>
        @endisset

        @if ($radio)
            <th
                class="{{ theme_style($theme, 'table.header.th') }}"
            >
            </th>
        @endif

        @if ($checkbox)
            @include('livewire-powergrid::components.checkbox-all')
        @endif

        @foreach ($columns as $column)
            <x-livewire-powergrid::cols
                wire:key="cols-{{ data_get($column, 'field') }} }}"
                :$column
                :$theme
                :$enabledFilters
            />
        @endforeach

        @if (isset($actions) && count($actions))
            @php
                $responsiveActionsColumnName =
                    \PowerComponents\LivewirePowerGrid\Components\SetUp\Responsive::ACTIONS_COLUMN_NAME;

                $isActionFixedOnResponsive =
                    isset($this->setUp['responsive']) &&
                    in_array($responsiveActionsColumnName, data_get($this->setUp, 'responsive.fixedColumns'))
                        ? true
                        : false;
            @endphp

            <th
                @if ($isActionFixedOnResponsive) fixed @endif
                class="{{ theme_style($theme, 'table.header.th') . ' ' . theme_style($theme, 'table.header.thAction') }}"
                scope="col"
                colspan="999"
                wire:key="{{ md5('actions') }}"
            >
                {{ trans('livewire-powergrid::datatable.labels.action') }}
            </th>
        @endif
    @endif
</tr>
