<div>
    @php
        $responsiveCheckboxColumnName = \PowerComponents\LivewirePowerGrid\Components\SetUp\Responsive::CHECKBOX_COLUMN_NAME;

        $isCheckboxFixedOnResponsive =
            isset($this->setUp['responsive']) &&
            in_array($responsiveCheckboxColumnName, data_get($this->setUp, 'responsive.fixedColumns'))
                ? true
                : false;
    @endphp
    <th
            @if ($isCheckboxFixedOnResponsive) fixed @endif
    scope="col"
            class="{{ theme_style($theme, 'checkbox.th') }}"
            wire:key="{{ md5('checkbox-all') }}"
    >
        <div
                class="{{ theme_style($theme, 'checkbox.base') }}"
        >
            <label
                    class="{{ theme_style($theme, 'checkbox.label') }}"
            >
                <input
                        class="{{ theme_style($theme, 'checkbox.input') }}"
                        type="checkbox"
                        wire:click="selectCheckboxAll"
                        wire:model="checkboxAll"
                >
            </label>
        </div>
    </th>
</div>
