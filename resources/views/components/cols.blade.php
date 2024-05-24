@props([
    'column' => null,
    'theme' => null,
    'enabledFilters' => null,
    'actions' => null,
    'dataField' => null,
])
@php
    $field = filled($column->dataField) ? $column->dataField : $column->field;

    $isFixedOnResponsive = false;

    if (isset($this->setUp['responsive'])) {
        if (in_array($field, data_get($this->setUp, 'responsive.fixedColumns'))) {
            $isFixedOnResponsive = true;
        }

        if ($column->fixedOnResponsive) {
            $isFixedOnResponsive = true;
        }
    }

    $sortOrder = isset($this->setUp['responsive'])
        ? data_get($this->setUp, "responsive.sortOrder.{$field}", null)
        : null;
@endphp
<th
    @if ($sortOrder) sort_order="{{ $sortOrder }}" @endif
    class="{{ data_get($theme, 'table.thClass') . ' ' . $column->headerClass }}"
    @if ($isFixedOnResponsive) fixed @endif
    @if ($column->sortable) x-multisort-shift-click="{{ $this->getId() }}" wire:click="sortBy('{{ $field }}')" @endif
    style="{{ $column->hidden === true ? 'display:none' : '' }}; width: max-content; @if ($column->sortable) cursor:pointer; @endif {{ data_get($theme, 'table.thStyle') . ' ' . $column->headerStyle }}"
>
    <div
        @class(['flex gap-2' => !isBootstrap5(), data_get($theme, 'cols.divClass')])
        style="{{ data_get($theme, 'cols.divStyle') }}"
    >
        <span data-value>{!! $column->title !!}</span>

        @if ($column->sortable)
            <x-dynamic-component
                    component="{{ $this->sortIcon($field) }}"
                    width="16"
            />
        @endif
    </div>
</th>
