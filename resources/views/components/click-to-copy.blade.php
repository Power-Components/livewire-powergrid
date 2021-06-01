@props([
    'row' => null,
    'field' => null,
    'enabled' => null,
    'label' => null
])
<div>
    @if($enabled)
        <button
            style="width: 24px; height: 30px; background-repeat: no-repeat;"
            onclick="copyToClipboard(this)" value="copy"
            class="img_copy" data-value="{{ $field }}"
            title="{{ $label }}">
        </button>
    @endif
</div>
