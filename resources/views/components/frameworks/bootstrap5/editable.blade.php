<x-livewire-powergrid::editable
    :tableName="$tableName"
    :primaryKey="$primaryKey"
    :row="$row"
    :field="$field"
    :theme="$theme"
    :currentTable="$currentTable"
    :showErrorBag="$showErrorBag"
>
    <x-slot name="input">
        <input
            type="text"
            x-on:keydown.enter="save()"
            x-on:keydown.esc="cancel"
            @if(data_get($editable, 'saveOnMouseOut')) x-on:mousedown.outside="save()" @endif
            :class="{'cursor-pointer': !editable}"
            class="{{ $theme->editable->inputClass }}"
            style="width: 100%"
            x-ref="editable"
            x-text="content"
            :value="$root.firstElementChild.innerText">
    </x-slot>
</x-livewire-powergrid::editable>
