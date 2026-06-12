{{-- PowerGrid Lite: Columns (thead) wrapper --}}
<thead
    {{ $attributes->merge(['class' => theme('table.layout.thead')]) }}
    @if($sticky) style="position: sticky; top: 0; z-index: 10;" @endif
>
    <tr class="{{ theme('table.layout.tr') }}">
        {{ $slot }}
    </tr>
</thead>
