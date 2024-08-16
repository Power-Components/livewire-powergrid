@php
    $inputAttributes = new \Illuminate\View\ComponentAttributeBag([
        'class' => data_get($theme, 'checkbox.inputClass'),
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
        class="{{ data_get($theme, 'checkbox.thClass') }}"
        style="{{ data_get($theme, 'checkbox.thStyle') }}"
    >
    </td>
@elseif($disable)
    <td
        class="{{ data_get($theme, 'checkbox.thClass') }}"
        style="{{ data_get($theme, 'checkbox.thStyle') }}"
    >
        <div class="{{ data_get($theme, 'checkbox.divClass') }}">
            <label class="{{ data_get($theme, 'checkbox.labelClass') }}">
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
        class="{{ data_get($theme, 'checkbox.thClass') }}"
        style="{{ data_get($theme, 'checkbox.thStyle') }}"
    >
        <div class="{{ data_get($theme, 'checkbox.divClass') }}">
            <label class="{{ data_get($theme, 'checkbox.labelClass') }}">
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
