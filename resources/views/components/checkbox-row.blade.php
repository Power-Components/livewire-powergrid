@props([
    'theme' => null,
    'checkbox' => null,
    'attribute' => null
])
@if($checkbox)
    <td class="{{ $theme->thClass }}" style="{{ $theme->thStyle }}">
        <div class="{{ $theme->divClass }}">
            <label class="{{ $theme->labelClass }}">
                <input class="{{ $theme->inputClass }}" type="checkbox" wire:model.defer="checkboxValues"
                       value="{{ $attribute }}">
            </label>
        </div>
    </td>
@endif
