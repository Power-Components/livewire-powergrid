@props([
    'column' => null,
    'theme' => null,
    'enabledFilters' => null,
    'actions' => null,
    'dataField' => null,
])
@php
    $field = filled($column->dataField) ? $column->dataField : $column->field;

    $isFixedOnResponsive = isset($this->setUp['responsive']) && in_array($field, data_get($this->setUp, 'responsive.fixedColumns')) ? true : false;
@endphp
<th
    class="{{ $theme->table->thClass . ' ' . $column->headerClass }}"
    wire:key="{{ md5($column->field) }}"
    @if($isFixedOnResponsive) fixed @endif
    @if ($column->sortable) x-data x-multisort-shift-click="{{ $this->getLivewireId() }}" wire:click="sortBy('{{ $field }}')" @endif
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
