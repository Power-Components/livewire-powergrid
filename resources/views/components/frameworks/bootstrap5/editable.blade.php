<x-livewire-powergrid::editable
    :$tableName
    :primaryKey="$this->realPrimaryKey"
    :$row
    :$field
    :$theme
    :$currentTable
    :$showErrorBag
>
    <x-slot name="input">
        <div
            x-ref="editable"
            x-text="content"
            value="{{ html_entity_decode($row->{$field}, ENT_QUOTES, 'utf-8') }}"
            placeholder="{{ html_entity_decode($row->{$field}, ENT_QUOTES, 'utf-8') }}"
            contenteditable
            class="pg-single-line {{ theme_style($theme, 'editable.input') }}"
            @if (data_get($editable, 'saveOnMouseOut')) x-on:mousedown.outside="save()" @endif
            x-on:keydown.enter="save()"
            :id="`editable-` + dataField + `-` + id"
            x-on:keydown.esc="cancel"
        >
        </div>
    </x-slot>
</x-livewire-powergrid::editable>
