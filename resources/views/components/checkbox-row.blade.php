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
                        @if (isset($ruleSetAttribute['attribute'])) {{ $attributes->merge([$ruleSetAttribute['attribute'] => $ruleSetAttribute['value']])->class($theme->checkbox->inputClass) }} @endif
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
                        type="checkbox"
                        x-on:click="window.Alpine.store('pgBulkActions').add($event.target.value, '{{ $tableName }}')"
                        wire:model="checkboxValues"
                        value="{{ $attribute }}"
                    >
                </label>
            </div>
        </td>
    @endif
@endif
