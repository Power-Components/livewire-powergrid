@props([
    'column' => null,
    'enabledFilters' => null,
    'actions' => null,
    'dataField' => null,
    '__partial' => null,
])
@php
    $__partial = $__partial ?? $this;
    $setUp = $__partial->setUp;
    $tableName = $__partial->tableName;
    $multiSort = $__partial->multiSort;
    $sortArray = $__partial->sortArray;
    $sortField = $__partial->sortField;
    $sortDirection = $__partial->sortDirection;

    $field = data_get($column, 'dataField', data_get($column, 'field'));

    $isFixedOnResponsive = false;

    if (isset($setUp['responsive'])) {
        if (in_array($field, data_get($setUp, 'responsive.fixedColumns'))) {
            $isFixedOnResponsive = true;
        }

        if (
            data_get($column, 'isAction') &&
            in_array(
                \PowerComponents\Turbine\Components\SetUp\Responsive::ACTIONS_COLUMN_NAME,
                data_get($setUp, 'responsive.fixedColumns'),
            )
        ) {
            $isFixedOnResponsive = true;
        }

        if (data_get($column, 'fixedOnResponsive')) {
            $isFixedOnResponsive = true;
        }
    }

    $sortOrder = isset($setUp['responsive'])
        ? data_get($setUp, "responsive.sortOrder.{$field}", null)
        : null;
@endphp
<th wire:key="cols-{{ $field }}-{{ $tableName }}"
    x-data
    data-column="{{ data_get($column, 'isAction') ? 'actions' : $field }}"
    @if ($sortOrder) sort_order="{{ $sortOrder }}" @endif
    @if ($isFixedOnResponsive) fixed @endif
    @if (data_get($column, 'enableSort')) x-multisort-shift-click="{{ $__partial->getId() }}"
    wire:click="sortBy('{{ $field }}')" @endif
    @class([
        (data_get($column, 'isAction') ? theme('table.layout.th_actions') : theme('table.layout.th')) => true,
        data_get($column, 'headerClass') => true,
    ])
    @style([
        'display:none' => data_get($column, 'hidden') === true,
        'cursor:pointer' => data_get($column, 'enableSort'),
        data_get($column, 'headerStyle') => filled(data_get($column, 'headerStyle')),
        'width: max-content !important',
    ])
>
    <div class="{{ theme('cols.div') }}">
        <span data-value>{!! data_get($column, 'title') !!}</span>

        @if (data_get($column, 'enableSort'))
            @include($__partial->showSortIcon($field), ['attributes' => new \Illuminate\View\ComponentAttributeBag(['width' => 16, 'height' => 16])])
        @endif
    </div>
</th>
