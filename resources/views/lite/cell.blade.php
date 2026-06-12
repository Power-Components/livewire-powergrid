{{-- PowerGrid Lite: Cell (td) --}}
<td
    {{ $attributes->merge(['class' => trim(theme('table.layout.td') . ' ' . $alignmentClass() . ' ' . $variantClass())]) }}
    @if($sticky) style="position: sticky; left: 0; z-index: 5;" @endif
>
    {{ $slot }}
</td>
