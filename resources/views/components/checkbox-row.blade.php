@php
    $rulesValues = $actionRulesClass->recoverFromAction($row, 'pg:checkbox');

    $inputAttributes = new \Illuminate\View\ComponentAttributeBag([
        'class' => data_get($theme, 'checkbox.inputClass'),
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
        class="{{ data_get($theme, 'checkbox.thClass') }}"
        style="{{ data_get($theme, 'checkbox.thStyle') }}"
    >
    </td>
@elseif(filled($rulesValues['disable']))
    <td
        class="{{ data_get($theme, 'checkbox.tdClass') }}"
        style="{{ data_get($theme, 'checkbox.tdStyle') }}"
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
        style="{{ data_get($theme, 'checkbox.thStyle')  }}"
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
