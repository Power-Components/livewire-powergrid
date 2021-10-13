@props([
    'theme' => null
])
<div>

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

</div>
