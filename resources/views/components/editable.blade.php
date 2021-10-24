@props([
    'primaryKey' => null,
    'row' => null,
    'field' => null,
    'theme' => null
])

<div x-data="{
       editable: false,
       id: '{{ $row->{$primaryKey} }}',
       field: '{{ $field }}',
       content: '{{ addslashes($row->{$field}) }}'
    }">
    <div x-text="content"
         style="border-bottom: dotted 1px; cursor: pointer"
         x-show="!editable"
         x-on:dblclick="editable = true"
    ></div>
    <div x-cloak
         x-show="editable">
        <input
            type="text"
            x-on:dblclick="editable = true"
            x-on:keydown.enter="sendEventInputChanged($event, id, field); editable = false; content = $event.target.value"
            x-on:blur="sendEventInputChanged($event, id, field); editable = false; content = $event.target.value"
            :class="{'cursor-pointer': !editable}"
            class="{{ $theme->inputClass }} p-2"
            x-ref="editable"
            x-text="content"
            :value="content">
    </div>
</div>

<script>
    function sendEventInputChanged(event, id, field) {
        document.getElementsByClassName('message')[0].style.display = "none";
        window.livewire.emit('eventInputChanged', {
            id: id,
            value: event.target.value,
            field: field
        })
    }
</script>
