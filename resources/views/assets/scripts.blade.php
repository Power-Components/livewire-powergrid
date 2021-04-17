<script src="{{ config('livewire-powergrid.plugins.flat_piker.js') }}"></script>
<script src="{{ config('livewire-powergrid.plugins.flat_piker.translate') }}"></script>

@if(config('livewire-powergrid.theme') === 'bootstrap')
    <script src="{{ config('livewire-powergrid.plugins.bootstrap-select.js') }}" crossorigin="anonymous"></script>
@endif
<script>

    @if(config('livewire-powergrid.theme') === 'tailwind')

    function saveEditableInput(event, id, field) {
        document.getElementsByClassName('message')[0].style.display = "none";
        window.livewire.emit('inputChanged', {
            id: id,
            value: event.target.value,
            field: field
        })
    }

    function saveToggleableInput(id, field, value) {
        document.getElementsByClassName('message')[0].style.display = "none";
        window.livewire.emit('toggleChanged', {
            id: id,
            field: field,
            value: value
        })
    }

    function returnValue(id, value, field) {
        document.getElementsByClassName('message')[0].style.display = "none";
        return '<input value="' + value + '" class="block bg-green-200 text-black-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" @keydown.enter=saveEditableInput($event,' + id + ',"' + field + '") >';
    }

    @elseif(config('livewire-powergrid.theme') === 'bootstrap')

    $(function () {
        $('.livewire_powergrid_select').selectpicker();
    })

    function toggle(event) {
        const id = event.target.getAttribute('data-id');
        const field = event.target.getAttribute('data-field');
        const checked = event.target.checked;
        saveToggleableInput(id, field, checked);
    }

    document.addEventListener('DOMContentLoaded', () => {
        Livewire.hook('message.processed', (message, component) => {
            $('.livewire_powergrid_select').selectpicker()
            $('.spinner').html('')
        })
    })

    function saveEditableInput(event, id, field) {
        document.getElementsByClassName('message')[0].style.display = "none";
        window.livewire.emit('inputChanged', {
            id: id,
            value: event.target.value,
            field: field
        })
    }

    function saveToggleableInput(id, field, value) {
        document.getElementsByClassName('message')[0].style.display = "none";
        window.livewire.emit('toggleChanged', {
            id: id,
            field: field,
            value: value
        })
    }

    function returnValue(id, value, field) {
        return '<input value="' + value + '" class="form-control" @keydown.enter=saveEditableInput($event,' + id + ',"' + field + '") >';
    }

    @endif

    function copyToClipboard(button) {
        const el = document.createElement('textarea');
        el.value = button.getAttribute('data-value');
        document.body.appendChild(el);
        el.select();
        document.execCommand('copy');
        document.body.removeChild(el);
    }
</script>
@stack('powergrid_scripts')

