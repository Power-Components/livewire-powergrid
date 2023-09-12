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
    
    $sortOrder = isset($this->setUp['responsive']) ? data_get($this->setUp, "responsive.sortOrder.{$field}", null) : null;
@endphp
<th
    @if ($sortOrder) sort_order="{{ $sortOrder }}" @endif
    class="{{ $theme->table->thClass . ' ' . $column->headerClass }}"
    @if ($isFixedOnResponsive) fixed @endif
    @if ($column->sortable) x-multisort-shift-click="{{ $this->getId() }}" wire:click="sortBy('{{ $field }}')" @endif
    style="{{ $column->hidden === true ? 'display:none' : '' }}; width: max-content; @if ($column->sortable) cursor:pointer; @endif {{ $theme->table->thStyle . ' ' . $column->headerStyle }}"
>
    <div
        @class([
            'pl-[11px]' => !$column->sortable && isTailwind(),
            $theme->cols->divClass,
        ])
        style="{{ $theme->cols->divStyle }}"
    >
        @if ($column->sortable)
            <span>
                {{ $this->sortLabel($field) }}
            </span>
        @else
            <span style="width: 6px"></span>
        @endif
        <span>{{ $column->title }}</span>
    </div>
</th>
