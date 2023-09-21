@php
    $rulesValues = $actionRulesClass->recoverFromAction($row, 'pg:radio');
    
    $inputAttributes = new \Illuminate\View\ComponentAttributeBag([
        'class' => $theme->radio->inputClass,
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
        class="{{ $theme->radio->thClass }}"
        style="{{ $theme->radio->thStyle }}"
    >
    </td>
@elseif(filled($rulesValues['disable']))
    <td
        class="{{ $theme->radio->thClass }}"
        style="{{ $theme->radio->thStyle }}"
    >
        <div class="{{ $theme->radio->divClass }}">
            <label class="{{ $theme->radio->labelClass }}">
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
        class="{{ $theme->radio->thClass }}"
        style="{{ $theme->radio->thStyle }}"
    >
        <div class="{{ $theme->radio->divClass }}">
            <label class="{{ $theme->radio->labelClass }}">
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
