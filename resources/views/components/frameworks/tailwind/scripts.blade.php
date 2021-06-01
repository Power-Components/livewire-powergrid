<script>
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
        document.getElementsByClassName('message')[0].style.display = "none";
        return '<input @keydown.enter=saveEditableInput($event,' + id + ',"' + field + '") value="' + value + '" class="block bg-green-200 text-black-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500">';
    }
</script>
