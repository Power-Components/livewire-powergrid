@props([
    'row' => null,
    'field' => null,
    'enabled' => null,
    'label' => null
])
<div>
    @if($enabled)
        <button
            style="background: transparent; text-align: left;border: 0;padding: 4px;"
            onclick="copyToClipboard(this)" value="copy"
            data-value="{{ $field }}"
            title="{{ $label }}">
        <x-livewire-powergrid::icons.copy/>
        </button>
    @endif
</div>
