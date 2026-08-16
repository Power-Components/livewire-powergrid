@props([
    '__partial' => null,
])

@php
    $__partial = $__partial ?? $this;
    $setUp = $__partial->setUp;
    $tableName = $__partial->tableName;
@endphp

@php
    $responsiveCheckboxColumnName =
        \PowerComponents\Turbine\Components\SetUp\Responsive::CHECKBOX_COLUMN_NAME;

    $isCheckboxFixedOnResponsive =
        isset($setUp['responsive']) &&
        in_array($responsiveCheckboxColumnName, data_get($setUp, 'responsive.fixedColumns'));
@endphp
<th
    @if ($isCheckboxFixedOnResponsive) fixed @endif
    scope="col"
    @class([theme('table.layout.th'), theme('table.checkbox.th')])
    wire:key="checkbox-all-{{ $tableName }}"
>
    <div class="{{ theme('table.checkbox.base') }}">
        <label class="{{ theme('table.checkbox.label') }}">
            <input
                class="{{ theme('table.checkbox.input') }}"
                type="checkbox"
                wire:click="selectCheckboxAll"
                wire:model="checkboxAll"
            >
        </label>
    </div>
</th>
