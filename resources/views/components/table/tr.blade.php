@props([
    'loading' => false,
])
<tr
    class="{{ $theme->table->trClass }}"
    style="{{ $theme->table->trStyle }}"
>
    @if ($loading)
        <td
            class="{{ $theme->table->tdBodyEmptyClass }}"
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
                class="{{ $theme->table->thClass }}"
                style="{{ $theme->table->thStyle }}"
                wire:key="show-collapse-{{ $tableName }}"
            >
            </th>
        @endif

        @isset($setUp['responsive'])
            <th
                fixed
                x-show="hasHiddenElements"
                class="{{ $theme->table->thClass }}"
                style="{{ $theme->table->thStyle }}"
            >
            </th>
        @endisset

        @if ($radio)
            <th
                class="{{ $theme->table->thClass }}"
                style="{{ $theme->table->thStyle }}"
            >
            </th>
        @endif

        @if ($checkbox)
            <x-livewire-powergrid::checkbox-all
                :checkbox="$checkbox"
                :theme="$theme->checkbox"
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
                class="{{ $theme->table->thClass . ' ' . $theme->table->thActionClass }}"
                scope="col"
                style="{{ $theme->table->thStyle . ' ' . $theme->table->thActionStyle }}"
                colspan="{{ count($actions) }}"
                wire:key="{{ md5('actions') }}"
            >
                {{ trans('livewire-powergrid::datatable.labels.action') }}
            </th>
        @endif
    @endif
</tr>
