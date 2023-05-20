@if (filled(config('livewire-powergrid.plugins.flatpickr.css')))
    <link
        rel="stylesheet"
        href="{{ config('livewire-powergrid.plugins.flatpickr.css') }}"
    >
@endif

@isset($cssPath)
    <style>
        {!! file_get_contents($cssPath) !!}
    </style>
@endisset
