@php
    $inputAttributes = new \Illuminate\View\ComponentAttributeBag([
        'class' => $theme->checkbox->inputClass
    ]);

    if (isset($ruleSetAttribute['attribute'])) {
        $inputAttributes = $inputAttributes->merge([
            $ruleSetAttribute['attribute'] => $ruleSetAttribute['value']
        ]);
    }
@endphp
@if ($checkbox)
    @if ($ruleHide)
        <td
            class="{{ $theme->checkbox->thClass }}"
            style="{{ $theme->checkbox->thStyle }}"
        >
        </td>
    @elseif($ruleDisable)
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
@endif
