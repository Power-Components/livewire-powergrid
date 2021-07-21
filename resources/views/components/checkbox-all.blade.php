@props([
    'checkbox' => null,
    'theme' => null
])
<div>
    @if($checkbox)
        <th scope="col" class="{{ $theme->thClass }}" style="{{ $theme->thStyle }}">
            <div class="{{ $theme->divClass }}">
                <label class="{{ $theme->labelClass }}">
                    <input class="{{ $theme->inputClass }}"
                           type="checkbox"
                           wire:click="selectCheckboxAll()"
                           wire:model.defer="checkboxAll">
                </label>
            </div>
        </th>
    @endif
</div>
