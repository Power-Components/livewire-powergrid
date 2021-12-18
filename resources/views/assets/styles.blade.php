<link rel="stylesheet" href="{{ config('livewire-powergrid.plugins.flat_piker.css') }}">

@if(isBootstrap5())
    <link rel="stylesheet" href="{{ config('livewire-powergrid.plugins.bootstrap-select.css') }}">
@endif

@isset($cssPath)
    <style>{!! file_get_contents($cssPath) !!}</style>
@endisset

