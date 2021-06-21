<script src="{{ config('livewire-powergrid.plugins.flat_piker.js') }}"></script>
<script src="{{ config('livewire-powergrid.plugins.flat_piker.translate') }}"></script>

@push('powergrid_styles')
    @includeIf(\PowerComponents\LivewirePowerGrid\Themes\ThemeBase::styles())
@endpush

<script>

    @if(powerGridTheme() === 'bootstrap')

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

@if(powerGridJsFramework() === JS_FRAMEWORK_ALPINE)
    <script src="{{ config('livewire-powergrid.js_framework_cdn.alpinejs') }}" defer></script>
@endif

@stack('power_grid_scripts')

