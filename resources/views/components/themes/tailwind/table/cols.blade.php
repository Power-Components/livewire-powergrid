@props([
    'column' => null,
    'columns' => null,
    'enabledFilters' => null,
    'actions' => null,
    'dataField' => null,
    '__partial' => null,
])
@use('PowerComponents\LivewirePowerGrid\Support\ColumnViewModel')
@use('PowerComponents\Turbine\Components\SetUp\Responsive')
@php
    $__partial = $__partial ?? $this;
    $setUp = $__partial->setUp;
    $headerColumns = $column !== null
        ? [$column]
        : ($columns ?? $__partial->columns);
@endphp
@foreach ($headerColumns as $column)
@php
    $field = data_get($column, 'dataField', data_get($column, 'field'));

    $isFixedOnResponsive = isset($setUp['responsive'])
        && Responsive::isColumnFixed($column, (array) data_get($setUp, 'responsive.fixedColumns', []));

    $sortOrder = isset($setUp['responsive'])
        ? Responsive::columnSortOrder($column, (array) data_get($setUp, 'responsive.sortOrder', []))
        : null;

    $alignClasses = ColumnViewModel::alignmentClasses(data_get($column, 'align'));
@endphp
<th wire:key="cols-{{ $field }}-{{ $__partial->tableName }}"
    x-data
    data-column="{{ data_get($column, 'isAction') ? 'actions' : $field }}"
    @if ($sortOrder) sort_order="{{ $sortOrder }}" @endif
    @if ($isFixedOnResponsive) fixed @endif
    @if (data_get($column, 'enableSort')) x-multisort-shift-click="{{ $__partial->getId() }}"
    wire:click="sortBy('{{ $field }}')" @endif
    @class([
        theme('table.layout.th') => true,
        data_get($column, 'headerClass') => true,
    ])
    @style([
        'display:none' => data_get($column, 'hidden') === true,
        'cursor:pointer' => data_get($column, 'enableSort'),
        data_get($column, 'headerStyle') => filled(data_get($column, 'headerStyle')),
        'width: max-content !important',
    ])
>
    <div @class([theme('cols.div'), $alignClasses])>
        <span data-value>{!! data_get($column, 'title') !!}</span>

        @if (data_get($column, 'enableSort'))
            @php $sortIcon = $__partial->sortIconComponent($field); @endphp
            @if ($sortIcon === 'chevron-up')
                <x-livewire-powergrid::icons.chevron-up width="16" height="16" />
            @elseif ($sortIcon === 'chevron-down')
                <x-livewire-powergrid::icons.chevron-down width="16" height="16" />
            @else
                <x-livewire-powergrid::icons.chevron-up-down width="16" height="16" />
            @endif
        @endif
    </div>
</th>
@endforeach
