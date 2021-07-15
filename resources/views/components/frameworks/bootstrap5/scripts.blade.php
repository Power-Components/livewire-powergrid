<script>
    $(function () {
        $('.power_grid_select').selectpicker();
    })

    function toggle(event) {
        const id = event.target.getAttribute('data-id');
        const field = event.target.getAttribute('data-field');
        const checked = event.target.checked;
        saveToggleableInput(checked, id, field);
    }

    document.addEventListener('DOMContentLoaded', () => {
        Livewire.hook('message.processed', (message, component) => {
            $('.power_grid_select').selectpicker()
        })
    })

    function saveEditableInput(event, id, field) {
        document.getElementsByClassName('message')[0].style.display = "none";
        window.livewire.emit('eventChangeInput', {
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
