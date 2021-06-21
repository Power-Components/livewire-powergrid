@props([
    'theme' => null,
    'checkbox' => null,
    'attribute' => null
])
@if($checkbox)
    <td class="{{ $theme->thClass }}" style="{{ $theme->thStyle }}">
        <div class="{{ $theme->divClass }}" wire:ignore.self>
            <label class="{{ $theme->labelClass }}">
                <input class="{{ $theme->inputClass }}" type="checkbox" wire:model="checkboxValues"
                       value="{{ $attribute }}">
            </label>
        </div>
    </td>
@endif
