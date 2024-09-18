@props([
    'column' => null,
    'enabledFilters' => null,
    'actions' => null,
    'dataField' => null,
    'theme' => null,
])
@php
    $field = data_get($column, 'dataField', data_get($column, 'field'));

    $isFixedOnResponsive = false;

    if (isset($this->setUp['responsive'])) {
        if (in_array($field, data_get($this->setUp, 'responsive.fixedColumns'))) {
            $isFixedOnResponsive = true;
        }

        if (
            data_get($column, 'isAction') &&
            in_array(
                \PowerComponents\LivewirePowerGrid\Components\SetUp\Responsive::ACTIONS_COLUMN_NAME,
                data_get($this->setUp, 'responsive.fixedColumns'),
            )
        ) {
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
    class="{{ theme_style($theme, 'table.header.th') . ' ' . data_get($column, 'headerClass') }}"
    @if ($isFixedOnResponsive) fixed @endif
    @if (data_get($column, 'enableSort')) x-multisort-shift-click="{{ $this->getId() }}"
        wire:click="sortBy('{{ $field }}')" @endif
    style="{{ data_get($column, 'hidden') === true ? 'display:none' : '' }}; width: max-content !important; @if (data_get($column, 'enableSort')) cursor:pointer; @endif {{ data_get($column, 'headerStyle') }}"
>
    <div
        class="{{ theme_style($theme, 'cols.div') }}"
    >
        <span data-value>{!! data_get($column, 'title') !!}</span>

        @if (data_get($column, 'enableSort'))
            <x-dynamic-component
                component="{{ $this->sortIcon($field) }}"
                width="16"
            />
        @endif
    </div>
</th>
