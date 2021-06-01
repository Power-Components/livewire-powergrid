@props([
    'theme' => null,
    'checkbox' => null,
    'attribute' => null
])
@if($checkbox)
    <td class="{{ $theme->checkbox->thClass }}" style="{{ $theme->checkbox->thStyle }}">
        <div wire:ignore.self>
            <label class="{{ $theme->checkbox->labelClass }}">
                <input class="{{ $theme->checkbox->inputClass }}" type="checkbox" wire:model="checkboxValues"
                       value="{{ $attribute }}">
            </label>
        </div>
    </td>
@endif
