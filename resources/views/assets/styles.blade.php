@if(filled(config('livewire-powergrid.plugins.flatpickr.css')))
<link rel="stylesheet" href="{{ config('livewire-powergrid.plugins.flatpickr.css') }}">
@endif

@if(isBootstrap5() && filled(config('livewire-powergrid.plugins.bootstrap-select.css')))
    <link rel="stylesheet" href="{{ config('livewire-powergrid.plugins.bootstrap-select.css') }}" crossorigin="anonymous"/>
@endif

@isset($cssPath)
    <style>{!! file_get_contents($cssPath) !!}</style>
@endisset

