@php
    $rulesValues = $actionRulesClass->recoverFromAction($row, 'pg:radio');

    $inputAttributes = new \Illuminate\View\ComponentAttributeBag([
        'class' => data_get($theme, 'radio.inputClass'),
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
        class="{{ data_get($theme, 'radio.tdClass') }}"
        style="{{ data_get($theme, 'radio.tdStyle') }}"
    >
    </td>
@elseif(filled($rulesValues['disable']))
    <td
        class="{{ data_get($theme, 'radio.tdClass') }}"
        style="{{ data_get($theme, 'radio.tdStyle') }}"
    >
        <div class="{{ data_get($theme, 'radio.divClass') }}">
            <label class="{{ data_get($theme, 'radio.labelClass')  }}">
                <input
                    {{ $inputAttributes }}
                    disabled
                    type="radio"
                >
            </label>
        </div>
    </td>
@else
    <td
        class="{{ data_get($theme, 'radio.thClass') }}"
        style="{{ data_get($theme, 'radio.thStyle') }}"
    >
        <div class="{{ data_get($theme, 'radio.divClass') }}">
            <label class="{{ data_get($theme, 'radio.labelClass') }}">
                <input
                    type="radio"
                    {{ $inputAttributes }}
                    wire:model.live="selectedRow"
                    value="{{ $attribute }}"
                >
            </label>
        </div>
    </td>
@endif
