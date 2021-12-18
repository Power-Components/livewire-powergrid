<script src="{{ config('livewire-powergrid.plugins.flat_piker.js') }}"></script>
<script src="{{ config('livewire-powergrid.plugins.flat_piker.translate') }}"></script>

@if(isBootstrap5())
    <script src="{{ config('livewire-powergrid.plugins.bootstrap-select.js') }}" crossorigin="anonymous"></script>
@endif

@if(filled(config('livewire-powergrid.alpinejs_cdn')))
    <script src="{{ config('livewire-powergrid.alpinejs_cdn') }}" defer></script>
@endif

@isset($jsPath)
    <script>{!! file_get_contents($jsPath) !!}</script>
@endisset
