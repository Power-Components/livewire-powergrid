<script src="{{ config('livewire-powergrid.plugins.flat_piker.js') }}"></script>
<script src="{{ config('livewire-powergrid.plugins.flat_piker.translate') }}"></script>

@includeIf(powerGridThemeRoot().".scripts")

@if(powerGridJsFramework() === JS_FRAMEWORK_ALPINE)
    <script src="{{ config('livewire-powergrid.js_framework_cdn.alpinejs') }}" defer></script>
@endif

@stack('power_grid_scripts')

