@props([
    'column' => null,
    'theme' => null,
    'enabledFilters' => null,
    'actions' => null,
    'dataField' => null,
])
@php
    $field = data_get($column, 'dataField', data_get($column, 'field'));

    $isFixedOnResponsive = false;

    if (isset($this->setUp['responsive'])) {
        if (in_array($field, data_get($this->setUp, 'responsive.fixedColumns'))) {
            $isFixedOnResponsive = true;
        }

        if (data_get($column, 'isAction') &&
            in_array(
                    \PowerComponents\LivewirePowerGrid\Responsive::ACTIONS_COLUMN_NAME,
                    data_get($this->setUp, 'responsive.fixedColumns')
            )) {
            $isFixedOnResponsive = true;
        }

        if (data_get($column, 'fixedOnResponsive')) {
            $isFixedOnResponsive = true;
        }
    }

    $sortOrder = isset($this->setUp['responsive'])
        ? data_get($this->setUp, "responsive.sortOrder.{$field}", null)
        : null;
@endphp
<th
    x-data="{ sortable: @js(data_get($column, 'sortable')) }"
    @if ($sortOrder) sort_order="{{ $sortOrder }}" @endif
    class="{{ data_get($theme, 'table.thClass') . ' ' . data_get($column, 'headerClass') }}"
    @if ($isFixedOnResponsive) fixed @endif
    @if (data_get($column, 'sortable')) x-multisort-shift-click="{{ $this->getId() }}" wire:click="sortBy('{{ $field }}')" @endif
    style="{{ data_get($column, 'hidden') === true ? 'display:none' : '' }}; width: max-content; @if (data_get($column, 'sortable')) cursor:pointer; @endif {{ data_get($theme, 'table.thStyle') . ' ' . data_get($column, 'headerStyle') }}"
>
    <div
        @class(['flex gap-2' => !isBootstrap5(), data_get($theme, 'cols.divClass')])
        style="{{ data_get($theme, 'cols.divStyle') }}"
    >
        <span data-value>{!! data_get($column, 'title') !!}</span>

        @if (data_get($column, 'sortable'))
            <x-dynamic-component
                    component="{{ $this->sortIcon($field) }}"
                    width="16"
            />
        @endif
    </div>
</th>
