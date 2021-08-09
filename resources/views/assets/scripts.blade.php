<script src="{{ config('livewire-powergrid.plugins.flat_piker.js') }}"></script>
<script src="{{ config('livewire-powergrid.plugins.flat_piker.translate') }}"></script>

@includeIf(powerGridThemeRoot()."scripts")

<script>
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

