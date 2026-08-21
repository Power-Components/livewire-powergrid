@php($element = ($__partial ?? $this)->headerElement('filters'))
<div
    wire:key="toggle-filters-{{ $tableName }}"
    id="toggle-filters"
    class="{{ theme('header.filters.wrapper') }}"
>
    <button
        wire:click="toggleFilters"
        type="button"
        title="{{ $element['title'] }}"
        aria-label="{{ $element['title'] }}"
        class="{{ theme('header.filters.button', theme('header.layout.actions')) }}"
    >
        {!! $element['iconHtml'] !!}
        @if ($element['showLabel'])
            <span class="{{ theme('header.filters.label') }}">{{ $element['title'] }}</span>
        @endif
    </button>
</div>
