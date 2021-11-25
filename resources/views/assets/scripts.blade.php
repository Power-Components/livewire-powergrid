<script src="{{ config('livewire-powergrid.plugins.flat_piker.js') }}"></script>
<script src="{{ config('livewire-powergrid.plugins.flat_piker.translate') }}"></script>

@includeIf(powerGridThemeRoot().".scripts")

<script>
    function copyToClipboard(button) {
        const el = document.createElement('textarea');
        el.value = button.getAttribute('data-value');
        document.body.appendChild(el);
        el.select();
        document.execCommand('copy');
        document.body.removeChild(el);
    }

    function htmlSpecialChars(string) {
        const el = document.createElement('div');
        el.innerText = string;
        return el.innerHTML;
    }

    function isV2() {
        return window.Alpine && window.Alpine.version && /^2\..+\..+$/.test(window.Alpine.version)
    }

    setTimeout(function () {
        if (isV2()) {
            console.warn('⚡️ Powergrid alert ⚡ ️- Alpine 2x will be discontinued soo.')
        }
    }, 1000)
</script>

@if(powerGridJsFramework() === JS_FRAMEWORK_ALPINE)
    <script src="{{ config('livewire-powergrid.js_framework_cdn.alpinejs') }}" defer></script>
@endif

@stack('power_grid_scripts')

