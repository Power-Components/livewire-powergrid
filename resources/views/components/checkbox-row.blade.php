@props([
    'theme' => null,
    'checkbox' => null,
    'hide'=> false,
    'disable' => false,
    'attribute' => null
])
@if($checkbox)
    @if($hide)
        <td class="{{ $theme->thClass }}" style="{{ $theme->thStyle }}">
            <div class="{{ $theme->divClass }}">
            </div>
        </td>
    @elseif($disable)
        <td class="{{ $theme->thClass }}" style="{{ $theme->thStyle }}">
            <div class="{{ $theme->divClass }}">
                <label class="{{ $theme->labelClass }}">
                    <input class="{{ $theme->inputClass }}" disabled type="checkbox">
                </label>
            </div>
        </td>
    @else
        <td class="{{ $theme->thClass }}" style="{{ $theme->thStyle }}">
            <div class="{{ $theme->divClass }}">
                <label class="{{ $theme->labelClass }}">
                    <input class="{{ $theme->inputClass }}" type="checkbox" wire:model.defer="checkboxValues"
                           value="{{ $attribute }}">
                </label>
            </div>
        </td>
    @endif
    @endif
