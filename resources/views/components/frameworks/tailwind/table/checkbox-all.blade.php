<div>
    @php
        $responsiveCheckboxColumnName =
            \PowerComponents\LivewirePowerGrid\Components\SetUp\Responsive::CHECKBOX_COLUMN_NAME;

        $isCheckboxFixedOnResponsive =
            isset($this->setUp['responsive']) &&
            in_array($responsiveCheckboxColumnName, data_get($this->setUp, 'responsive.fixedColumns'));
    @endphp
    <th
        @if ($isCheckboxFixedOnResponsive) fixed @endif
        scope="col"
        @class([theme('table.header.th'), theme('checkbox.th')])
        wire:key="checkbox-all-{{ $tableName }}"
    >
        <div class="{{ theme('checkbox.base') }}">
            <label class="{{ theme('checkbox.label') }}">
                <input
                    class="{{ theme('checkbox.input') }}"
                    type="checkbox"
                    wire:click="selectCheckboxAll"
                    wire:model="checkboxAll"
                >
            </label>
        </div>
    </th>
</div>
