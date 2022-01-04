<script>
    window.onload = function () {
        if (!window.Alpine) {
            console.warn('Oops. Could not find Alpine. Are you sure you installed it? See: https://alpinejs.dev/', {
                alpine: 'https://alpinejs.dev/',
                powergrid: 'https://github.com/Power-Components/livewire-powergrid',
            })
        } else {
            if (window.Alpine.version && /^2\..+\..+$/.test(window.Alpine.version)) {
                console.warn('Oops. Powergrid does not support V2.x', {
                    alpine: 'https://alpinejs.dev/',
                    powergrid: 'https://github.com/Power-Components/livewire-powergrid',
                })
            }
        }
    }
</script>

@if(filled(config('livewire-powergrid.plugins.flat_piker.js')))
    <script src="{{ config('livewire-powergrid.plugins.flat_piker.js') }}"></script>
@endif

@if(filled(config('livewire-powergrid.plugins.flat_piker.translate')))
    <script src="{{ config('livewire-powergrid.plugins.flat_piker.translate') }}"></script>
@endif

@if(isBootstrap5())
    <script src="{{ config('livewire-powergrid.plugins.bootstrap-select.js') }}" crossorigin="anonymous"></script>
@endif

@if(filled(config('livewire-powergrid.alpinejs_cdn')))
    <script src="{{ config('livewire-powergrid.alpinejs_cdn') }}" defer></script>
@endif

@isset($jsPath)
    <script>{!! file_get_contents($jsPath) !!}</script>
@endisset
