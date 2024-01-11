@props([
    'theme' => null,
])
<div>
    @php
        $responsiveCheckboxColumnName = PowerComponents\LivewirePowerGrid\Responsive::CHECKBOX_COLUMN_NAME;

        $isCheckboxFixedOnResponsive = isset($this->setUp['responsive']) && in_array($responsiveCheckboxColumnName, data_get($this->setUp, 'responsive.fixedColumns')) ? true : false;
    @endphp
    <th
        @if ($isCheckboxFixedOnResponsive) fixed @endif
        scope="col"
        class="{{ data_get($theme, 'thClass') }}"
        style="{{ data_get($theme, 'thStyle') }}"
        wire:key="{{ md5('checkbox-all') }}"
    >
        <div class="{{ data_get($theme, 'divClass') }}">
            <label class="{{ data_get($theme, 'labelClass') }}">
                <input
                    class="{{ data_get($theme, 'inputClass') }}"
                    type="checkbox"
                    wire:click="selectCheckboxAll"
                    wire:model="checkboxAll"
                >
            </label>
        </div>
    </th>
</div>
