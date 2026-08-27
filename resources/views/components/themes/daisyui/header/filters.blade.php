@php
    $__partial = $__partial ?? $this;
    $tableName = $tableName ?? $__partial->tableName;
    $element = $__partial->headerElement('filters');
    $count = $__partial->activeFilterCount();
@endphp
<div
    wire:partial="pg-filter-trigger-{{ $tableName }}"
    wire:key="toggle-filters-{{ $tableName }}"
    id="toggle-filters"
    class="{{ theme('header.filters.wrapper') }}"
>
    <button
        wire:click="toggleFilters"
        type="button"
        title="{{ $element['title'] }}"
        aria-label="{{ $element['title'] }}"
        class="{{ theme('header.filters.button', theme('header.layout.actions')) }} relative"
    >
        {!! $element['iconHtml'] !!}
        @if ($element['showLabel'])
            <span class="{{ theme('header.filters.label') }}">{{ $element['title'] }}</span>
        @endif
        @if ($count)
            <span
                data-cy="filter-flyout-badge"
                class="{{ theme('filter.dropdown.badge') }}"
            >{{ $count }}</span>
        @endif
    </button>
</div>
