@props([
    'row' => null,
    'field' => null,
    'enabled' => null,
    'label' => null
])
<div x-data="{ value: '{{ $field }}'}">
    @if($enabled)
        <button
            style="background: transparent; text-align: left;border: 0;padding: 4px;"
            x-on:click="$pgClipboard(value)"
            title="{{ $label }}">
        <x-livewire-powergrid::icons.copy/>
        </button>
    @endif
</div>
