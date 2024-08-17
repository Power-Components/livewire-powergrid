<div>
    @php
        $responsiveCheckboxColumnName = PowerComponents\LivewirePowerGrid\Responsive::CHECKBOX_COLUMN_NAME;

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
        style="{{ theme_style($theme, 'checkbox.th.1') }}"
        wire:key="{{ md5('checkbox-all') }}"
    >
        <div
            class="{{ theme_style($theme, 'checkbox.base') }}"
            style="{{ theme_style($theme, 'checkbox.base.1') }}"
        >
            <label
                class="{{ theme_style($theme, 'checkbox.label') }}"
                style="{{ theme_style($theme, 'checkbox.label.1') }}"
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
