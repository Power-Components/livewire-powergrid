@props([
    'checkbox' => null,
    'theme' => null
])
<div>
    @if($checkbox)
        <th scope="col" class="{{ $theme->checkbox->thClass }}">
            <label class="{{ $theme->checkbox->labelClass }}">
                <input class="{{ $theme->checkbox->inputClass }}"
                       type="checkbox"
                       wire:click="selectCheckboxAll()"
                       wire:model.lazy="checkboxAll">
            </label>
        </th>
    @endif
</div>
