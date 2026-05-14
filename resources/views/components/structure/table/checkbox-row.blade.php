@php
    $inputAttributes = new \Illuminate\View\ComponentAttributeBag([
        'class' => theme('checkbox.input'),
    ]);

    $rules = collect($row->__powergrid_rules)
        ->where('apply', true)
        ->where('forAction', \PowerComponents\LivewirePowerGrid\Components\Rules\RuleManager::TYPE_CHECKBOX)
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
        class="{{ theme('checkbox.th') }}"
    >
    </td>
@elseif($disable)
    <td
        wire:key="checkbox-row-disable-{{ $attribute }}"
        class="{{ theme('checkbox.th') }}"
    >
        <div class="{{ theme('checkbox.base') }}">
            <label class="{{ theme('checkbox.label') }}">
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
        class="{{ theme('checkbox.th') }}"
    >
        <div class="{{ theme('checkbox.base') }}">
            <label class="{{ theme('checkbox.label') }}">
                <input
                    x-data="{}"
                    type="checkbox"
                    {{ $inputAttributes }}
                    x-on:click="window.Alpine.store('pgBulkActions').add($event.target.value, '{{ $tableName }}')"
                    wire:model="checkboxValues"
                    value="{{ $attribute }}"
                >
            </label>
        </div>
    </td>
@endif
