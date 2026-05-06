@php
    $field = data_get($column, 'dataField', data_get($column, 'field'));

    $isFixedOnResponsive = false;

    if (isset($setUp['responsive'])) {
        if (in_array($field, data_get($setUp, 'responsive.fixedColumns'))) {
            $isFixedOnResponsive = true;
        }

        if (
            data_get($column, 'isAction') &&
            in_array(
                \PowerComponents\LivewirePowerGrid\Components\SetUp\Responsive::ACTIONS_COLUMN_NAME,
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
<th x-data="{ sortable: @js(data_get($column, 'sortable')) }"
    data-column="{{ data_get($column, 'isAction') ? 'actions' : $field }}"
    @if ($sortOrder) sort_order="{{ $sortOrder }}" @endif
    @if ($isFixedOnResponsive) fixed @endif
    @if (data_get($column, 'enableSort')) x-multisort-shift-click="{{ $tableName }}"
    wire:click="sortBy('{{ $field }}')" @endif
    @class([
        theme('table.th') => true,
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
            @include(sort_icon($field, $multiSort ?? false, $sortArray ?? [], $sortField ?? '', $sortDirection ?? 'asc'), ['attributes' => new \Illuminate\View\ComponentAttributeBag(['width' => 16, 'height' => 16])])
        @endif
    </div>
</th>
