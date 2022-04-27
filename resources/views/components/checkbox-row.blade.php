@if($checkbox)
    @if($ruleHide)
        <td class="{{ $theme->checkbox->thClass }}"
            style="{{ $theme->checkbox->thStyle }}">
        </td>
    @elseif($ruleDisable)
        <td class="{{ $theme->checkbox->thClass }}" style="{{ $theme->thStyle }}">
            <div class="{{ $theme->checkbox->divClass }}">
                <label class="{{ $theme->checkbox->labelClass }}">
                    <input @if(isset($ruleSetAttribute['attribute']))
                           {{ $attributes->merge([$ruleSetAttribute['attribute'] => $ruleSetAttribute['value']])->class($theme->checkbox->inputClass) }}
                           @else
                           class="{{ $theme->checkbox->inputClass }}"
                           @endif
                           disabled
                           type="checkbox">
                </label>
            </div>
        </td>
    @else
        <td class="{{ $theme->checkbox->thClass }}"
            style="{{ $theme->checkbox->thStyle }}">
            <div class="{{ $theme->checkbox->divClass }}">
                <label class="{{ $theme->checkbox->labelClass }}">
                    <input @if(isset($ruleSetAttribute['attribute']))
                           {{ $attributes->merge([$ruleSetAttribute['attribute'] => $ruleSetAttribute['value']])->class($theme->checkbox->inputClass) }}
                           @else
                           class="{{ $theme->checkbox->inputClass }}"
                           @endif
                           type="checkbox"
                           wire:model.defer="checkboxValues"
                           value="{{ $attribute }}">
                </label>
            </div>
        </td>
    @endif
@endif
