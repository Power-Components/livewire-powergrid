<x-livewire-powergrid::editable
    :tableName="$tableName"
    :primaryKey="$primaryKey"
    :row="$row"
    :field="$field"
    :theme="$theme"
    :currentTable="$currentTable"
    :showErrorBag="$showErrorBag"
    :editable="$editable"
>
    <x-slot name="input">
        <div
            x-ref="editable"
            x-text="content"
            value="{{ $row->{$field} }}"
            contenteditable
            class="{{ $theme->editable->inputClass }}"
            @if(data_get($editable, 'saveOnMouseOut')) x-on:mousedown.outside="save()" @endif
            x-on:keydown.enter="save()"
            x-on:keydown.esc="cancel">
        </div>
    </x-slot>
</x-livewire-powergrid::editable>
