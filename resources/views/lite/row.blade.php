{{-- PowerGrid Lite: Row (tr) --}}
<tr
    wire:key="pg-lite-row-{{ $key }}"
    {{ $attributes->merge(['class' => theme('table.layout.tr')]) }}
>
    @if($checkboxValue !== null)
        <td class="{{ theme('table.checkbox.th') }}">
            <label class="{{ theme('table.checkbox.label') }}">
                <input
                    type="checkbox"
                    value="{{ $checkboxValue }}"
                    wire:model.live="checkboxValues"
                    class="{{ theme('table.checkbox.input') }}"
                />
            </label>
        </td>
    @endif

    {{ $slot }}
</tr>
