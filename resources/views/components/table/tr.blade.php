@props([
    'loading' => false,
])
<tr class="{{ data_get($theme, 'table.trClass') }}"
    style="{{ data_get($theme, 'table.trStyle') }}"
>
    @if ($loading)
        <td
            class="{{ data_get($theme, 'table.tdBodyEmptyClass') }}"
            colspan="{{ ($checkbox ? 1 : 0) + count($columns) }}"
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
                class="{{ data_get($theme, 'table.thClass') }}"
                style="{{ data_get($theme, 'table.trStyle') }}"
                wire:key="show-collapse-{{ $tableName }}"
            >
            </th>
        @endif

        @isset($setUp['responsive'])
            <th
                fixed
                x-show="hasHiddenElements"
                class="{{ data_get($theme, 'table.thClass') }}"
                style="{{ data_get($theme, 'table.thStyle') }}"
            >
            </th>
        @endisset

        @if ($radio)
            <th
                class="{{ data_get($theme, 'table.thClass') }}"
                style="{{ data_get($theme, 'table.thStyle') }}"
            >
            </th>
        @endif

        @if ($checkbox)
            <x-livewire-powergrid::checkbox-all
                :checkbox="$checkbox"
                :theme="data_get($theme, 'checkbox')"
            />
        @endif

        @foreach ($columns as $column)
            <x-livewire-powergrid::cols
                wire:key="cols-{{ $column->field }} }}"
                :column="$column"
                :theme="$theme"
                :enabledFilters="$enabledFilters"
            />
        @endforeach

        @if (isset($actions) && count($actions))
            @php
                $responsiveActionsColumnName = PowerComponents\LivewirePowerGrid\Responsive::ACTIONS_COLUMN_NAME;

                $isActionFixedOnResponsive = isset($this->setUp['responsive']) && in_array($responsiveActionsColumnName, data_get($this->setUp, 'responsive.fixedColumns')) ? true : false;
            @endphp

            <th
                @if ($isActionFixedOnResponsive) fixed @endif
                class="{{ data_get($theme, 'table.thClass') . ' ' . data_get($theme, 'table.thActionClass') }}"
                scope="col"
                style="{{ data_get($theme, 'table.thStyle') . ' ' . data_get($theme, 'table.thActionStyle') }}"
                colspan="{{ count($actions) }}"
                wire:key="{{ md5('actions') }}"
            >
                {{ trans('livewire-powergrid::datatable.labels.action') }}
            </th>
        @endif
    @endif
</tr>
