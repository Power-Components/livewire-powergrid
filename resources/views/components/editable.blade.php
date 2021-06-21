@props([
    'row' => null,
    'field' => null,
    'theme' => null
])
<div x-data="{ value: '<span style=\'border-bottom: dotted 1px;\'>{{ addslashes($row->$field) }}</span>' }">
    <button
        style="width: 100%;{{ $theme->buttonClass }}"
        x-on:click="value = editableInput({{ $row->id }}, '{{ addslashes($row->$field)  }}', '{{ $field }}');"
        x-html="value"
    ></button>
</div>
<script>
    function editableInput(id, value, field) {
        document.getElementsByClassName('message')[0].style.display = "none";
        return '<input @keydown.enter=saveEditableInput($event,' + id + ',"' + field + '") value="' + value + '" ' +
            'class="{{ $theme->inputClass }}">';
    }

    function saveEditableInput(event, id, field) {
        document.getElementsByClassName('message')[0].style.display = "none";
        window.livewire.emit('eventChangeInput', {
            id: id,
            value: event.target.value,
            field: field
        })
    }
</script>

