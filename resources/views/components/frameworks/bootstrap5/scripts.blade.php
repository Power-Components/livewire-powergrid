<script src="{{ config('livewire-powergrid.plugins.bootstrap-select.js') }}" crossorigin="anonymous"></script>

<script>
    function saveEditableInput(event, id, field) {
        document.getElementsByClassName('message')[0].style.display = "none";
        window.livewire.emit('eventInputChanged', {
            id: id,
            value: event.target.value,
            field: field
        })
    }

    function editableInput(id, value, field) {
        return '<input value="' + value + '" class="form-control" @keydown.enter=saveEditableInput($event,' + id + ',"' + field + '") >';
    }
</script>
