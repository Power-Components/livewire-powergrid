<x-livewire-powergrid::editable
    :tableName="$tableName"
    :primaryKey="$this->realPrimaryKey"
    :row="$row"
    :field="$field"
    :currentTable="$currentTable"
    :showErrorBag="$showErrorBag"
    :editable="$editable"
>
    <x-slot name="input">
        <div
            x-ref="editable"
            x-text="content"
            value="{{ html_entity_decode(data_get($row, $field), ENT_QUOTES, 'utf-8') }}"
            placeholder="{{ html_entity_decode(data_get($row, $field), ENT_QUOTES, 'utf-8') }}"
            contenteditable
            class="pg-single-line {{ theme_style($this->theme, 'editable.input') }}"
            style="{{ theme_style($this->theme, 'editable.input.1')  }}"
            @if (data_get($editable, 'saveOnMouseOut')) x-on:mousedown.outside="save()" @endif
            x-on:keydown.enter="save()"
            :id="`editable-` + dataField + `-` + id"
            x-on:keydown.esc="cancel"
        >
        </div>
    </x-slot>
</x-livewire-powergrid::editable>
