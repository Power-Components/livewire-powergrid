@props([
    'columns' => null,
    'theme' => null,
    'tableName' => null,
    'filtersFromColumns' => null,
    'showFilters' => false,
    '__partial' => null,
])

@php
    $__partial = $__partial ?? (isset($this) ? $this : null);
    $tableName = $tableName ?? $__partial->tableName;
@endphp

<div
    x-data
    wire:partial="pg-filters-{{ $tableName }}"
    class="{{ theme('layout.outside_filters') }} mt-2 md:mt-0"
>
    <div
        x-show="$wire.showFilters"
        x-cloak
        x-transition:enter="transform duration-100"
        x-transition:enter-start="opacity-0 scale-90"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transform duration-100"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-90"
        class="pg-filter-container"
    >
        @include('livewire-powergrid::components.themes.tailwind.filters.fields', [
            'theme' => $theme,
            'tableName' => $tableName,
            'filtersFromColumns' => $filtersFromColumns,
            'gridClass' => 'grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 2xl:grid-cols-6 gap-3',
            '__partial' => $__partial,
        ])
    </div>
</div>
