{{-- PowerGrid Lite: Rows (tbody) wrapper --}}
<tbody {{ $attributes->merge(['class' => theme('table.layout.tbody')]) }}>
    {{ $slot }}
</tbody>
