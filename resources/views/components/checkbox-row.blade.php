@php
    $rulesValues = $actionRulesClass->recoverFromAction($row, 'pg:checkbox');

    $inputAttributes = new \Illuminate\View\ComponentAttributeBag([
        'class' => $theme->checkbox->inputClass,
    ]);

    if (filled($rulesValues['setAttributes'])) {
        foreach ($rulesValues['setAttributes'] as $rulesAttributes) {
            $inputAttributes = $inputAttributes->merge([
                $rulesAttributes['attribute'] => $rulesAttributes['value'],
            ]);
        }
    }
@endphp

@if (filled($rulesValues['hide']))
    <td
        class="{{ $theme->checkbox->thClass }}"
        style="{{ $theme->checkbox->thStyle }}"
    >
    </td>
@elseif(filled($rulesValues['disable']))
    <td
        class="{{ $theme->checkbox->thClass }}"
        style="{{ $theme->checkbox->thStyle }}"
    >
        <div class="{{ $theme->checkbox->divClass }}">
            <label class="{{ $theme->checkbox->labelClass }}">
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
        class="{{ $theme->checkbox->thClass }}"
        style="{{ $theme->checkbox->thStyle }}"
    >
        <div class="{{ $theme->checkbox->divClass }}">
            <label class="{{ $theme->checkbox->labelClass }}">
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
