@php
    $inputAttributes = new \Illuminate\View\ComponentAttributeBag([
        'class' => theme_style($theme, 'checkbox.input'),
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
        class="{{ theme_style($theme, 'checkbox.th') }}"
    >
    </td>
@elseif($disable)
    <td
        class="{{ theme_style($theme, 'checkbox.th') }}"
    >
        <div class="{{ theme_style($theme, 'checkbox.base') }}">
            <label class="{{ theme_style($theme, 'checkbox.label') }}">
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
        class="{{ theme_style($theme, 'checkbox.th') }}"
    >
        <div class="{{ theme_style($theme, 'checkbox.base') }}">
            <label class="{{ theme_style($theme, 'checkbox.label') }}">
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
