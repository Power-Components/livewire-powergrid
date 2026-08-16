@php
    $inputAttributes = new \Illuminate\View\ComponentAttributeBag([
        'class' => theme('table.checkbox.input'),
    ]);

    $rules = collect($row->__turbine_rules)
        ->where('apply', true)
        ->where('forAction', \PowerComponents\Turbine\Components\Rules\RuleManager::TYPE_CHECKBOX)
        ->last();

    if (isset($rules['attributes'])) {
        foreach ($rules['attributes'] as $key => $value) {
            $inputAttributes = $inputAttributes->merge([
                $key => $value,
            ]);
        }
    }

    $disable = (bool) data_get($rules, 'disable');
    $hide = (bool) data_get($rules, 'hide');

@endphp

@if ($hide)
    <td
        wire:key="checkbox-row-hide-{{ $attribute }}"
        class="{{ theme('table.checkbox.th') }}"
    >
    </td>
@elseif($disable)
    <td
        wire:key="checkbox-row-disable-{{ $attribute }}"
        class="{{ theme('table.checkbox.th') }}"
    >
        <div class="{{ theme('table.checkbox.base') }}">
            <label class="{{ theme('table.checkbox.label') }}">
                <input
                    {{ $inputAttributes }}
                    disabled
                    type="checkbox"
                >
            </label>
        </div>
    </td>
@else
    <td
        wire:key="checkbox-row-{{ $attribute }}"
        class="{{ theme('table.checkbox.th') }}"
    >
        <div class="{{ theme('table.checkbox.base') }}">
            <label class="{{ theme('table.checkbox.label') }}">
                <input
                    x-data
                    type="checkbox"
                    {{ $inputAttributes }}
                    x-on:click="$store.pgBulkActions.add($event.target.value, '{{ $tableName }}')"
                    wire:model="checkboxValues"
                    value="{{ $attribute }}"
                >
            </label>
        </div>
    </td>
@endif
