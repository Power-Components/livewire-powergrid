@php
    $inputAttributes = new \Illuminate\View\ComponentAttributeBag([
        'class' => $theme->radio->inputClass,
    ]);

    if (isset($ruleSetAttribute['attribute'])) {
        $inputAttributes = $inputAttributes->merge([
            $ruleSetAttribute['attribute'] => $ruleSetAttribute['value'],
        ]);
    }
@endphp
@if ($ruleHide)
    <td
        class="{{ $theme->radio->thClass }}"
        style="{{ $theme->radio->thStyle }}"
    >
    </td>
@elseif($ruleDisable)
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
                    id="radio-{{ $attribute }}"
                    type="radio"
                    {{ $inputAttributes }}
                    wire:model.live="selectedRadio"
                    value="{{ $attribute }}"
                >
            </label>
        </div>
    </td>
@endif
