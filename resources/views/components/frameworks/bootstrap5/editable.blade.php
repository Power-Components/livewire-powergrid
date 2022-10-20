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
        <div
            x-ref="editable"
            x-text="'{{ $row->{$field} }}'"
            value="{{ $row->{$field} }}"
            contenteditable
            class="pg-single-line {{ $theme->editable->inputClass }}"
            @if(data_get($editable, 'saveOnMouseOut')) x-on:mousedown.outside="save()" @endif
            x-on:keydown.enter="save()"
            :id="`editable-`+dataField+`-`+id"
            x-on:keydown.esc="cancel">
        </div>
    </x-slot>
</x-livewire-powergrid::editable>
