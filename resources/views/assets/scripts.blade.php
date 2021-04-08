<script src="{{ config('livewire-powergrid.plugins.flat_piker.js') }}"></script>
<script src="{{ config('livewire-powergrid.plugins.flat_piker.translate') }}"></script>

@if(config('livewire-powergrid.theme') === 'bootstrap')

    <script src="{{ config('livewire-powergrid.plugins.bootstrap-select.js') }}"
            crossorigin="anonymous"></script>
    <link rel="stylesheet"
          href="{{ config('livewire-powergrid.plugins.bootstrap-select.css') }}"
          crossorigin="anonymous"/>

    <script>
        $(function () {
            $('.livewire_powergrid_select').selectpicker()
        })
        document.addEventListener('DOMContentLoaded', () => {
            Livewire.hook('message.processed', (message, component) => {
                $('.livewire_powergrid_select').selectpicker()
                $('.spinner').html('')
            })
        })
    </script>
@endif

<script>

    @if(config('livewire-powergrid.theme') === 'tailwind')
        function updateEditableInput(event, id, field) {
            document.getElementsByClassName('loading')[0].style.display = "block";
            window.livewire.emit('editInput', {
                id: id,
                value: event.target.value,
                field: field
            })
        }
        function returnValue(id, value, field) {
            console.log(id, value)
            document.getElementsByClassName('icon_success')[0].style.display = "none";
            document.getElementsByClassName('icon_error')[0].style.display = "none";
            return '<input value=\''+value+'\' class=\'appearance-none block w-full bg-green-200 text-black-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500\' @keydown.enter=updateEditableInput($event,'+id+',\''+field+'\') >';
        }
        window.addEventListener('onUpdateInput', event => {
            document.getElementsByClassName('loading')[0].style.display = "none";

            if (event.detail.success === true) {
                document.getElementsByClassName('icon_success')[0].style.display = "block";
            } else {
                document.getElementsByClassName('icon_error')[0].style.display = "block";
            }
        })
    @endif

    @if(config('livewire-powergrid.theme') === 'bootstrap')
    function updateEditableInput(event, id, field) {
        window.livewire.emit('editInput', {
            id: id,
            value: event.target.value,
            field: field
        })
    }
    function returnValue(id, value, field) {
        console.log(id, value, field)
        return '<input value=\''+value+'\' class=\'form-control\' @keydown.enter=updateEditableInput($event,'+id+',\''+field+'\') >';
    }
    window.addEventListener('onUpdateInput', event => {
      console.log(event.detail)
    })
    @endif

</script>
@stack('powergrid_scripts')

