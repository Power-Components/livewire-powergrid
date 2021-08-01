<script src="{{ config('livewire-powergrid.plugins.bootstrap-select.js') }}" crossorigin="anonymous"></script>

<script>
    function toggle(event) {
        const id = event.target.getAttribute('data-id');
        const field = event.target.getAttribute('data-field');
        const checked = event.target.checked;
        saveToggleableInput(checked, id, field);
    }

    function saveEditableInput(event, id, field) {
        document.getElementsByClassName('message')[0].style.display = "none";
        window.livewire.emit('eventInputChanged', {
            id: id,
            value: event.target.value,
            field: field
        })
    }

    function saveToggleableInput(value, id, field) {
        document.getElementsByClassName('message')[0].style.display = "none";
        window.livewire.emit('eventToggleChanged', {
            id: id,
            field: field,
            value: value
        })
    }

    function editableInput(id, value, field) {
        return '<input value="' + value + '" class="form-control" @keydown.enter=saveEditableInput($event,' + id + ',"' + field + '") >';
    }
</script>
