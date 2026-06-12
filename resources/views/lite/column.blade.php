{{-- PowerGrid Lite: Column (th) --}}
@php
    $thClass = $checkbox
        ? theme('table.checkbox.th')
        : theme('table.layout.th');
@endphp

<th
    {{ $attributes->merge(['class' => trim($thClass . ' ' . $alignmentClass())]) }}
    @if($sticky) style="position: sticky; left: 0; z-index: 5;" @endif
>
    @if($checkbox)
        <label class="{{ theme('table.checkbox.label') }}">
            <input
                type="checkbox"
                wire:model.live="checkboxAll"
                class="{{ theme('table.checkbox.input') }}"
            />
        </label>
    @else
        <div class="{{ theme('cols.div') }}">
            <span>{{ $slot }}</span>

            @if($sortable)
                <span class="ml-1 inline-flex">
                    @include($sortIcon())
                </span>
            @endif
        </div>
    @endif
</th>
