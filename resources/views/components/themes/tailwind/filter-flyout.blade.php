@props([
    'theme' => null,
    'tableName' => null,
    'filtersFromColumns' => null,
    '__partial' => null,
])

@php
    $__partial = $__partial ?? (isset($this) ? $this : null);
    $tableName = $tableName ?? $__partial->tableName;

    $flyout = $__partial->filterFlyoutOptions();
    $isLeft = $flyout['position'] === 'left';

    // Alpine needs the off-screen class up front, so the side decides both the
    // anchoring classes and the direction the panel slides in from.
    $panelSide = $isLeft ? theme('filter.flyout.panel_left') : theme('filter.flyout.panel_right');
    $panelOffscreen = $isLeft ? '-translate-x-full' : 'translate-x-full';
@endphp

<div
    x-data
    wire:partial="pg-filters-{{ $tableName }}"
    wire:key="filter-flyout-{{ $tableName }}"
    @if ($flyout['close_on_escape'])
        {{-- Guarded: the listener is global, so an unguarded assignment would
             commit to the server on every Escape press anywhere on the page. --}}
        x-on:keydown.escape.window="$wire.showFilters && ($wire.showFilters = false)"
    @endif
>
    <div
        x-show="$wire.showFilters"
        x-cloak
        {{-- Inline display keeps the backdrop from flashing over the page when the
             host app has no [x-cloak] rule; x-show clears it once Alpine boots. --}}
        style="display: none"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @if ($flyout['close_on_click_outside'])
            x-on:click="$wire.showFilters = false"
        @endif
        class="pg-filter-flyout-overlay {{ theme('filter.flyout.overlay') }}"
    ></div>

    <div
        x-show="$wire.showFilters"
        x-cloak
        style="display: none"
        role="dialog"
        aria-modal="true"
        aria-labelledby="pg-filter-flyout-title-{{ $tableName }}"
        data-cy="filter-flyout"
        data-position="{{ $flyout['position'] }}"
        x-transition:enter="transform transition ease-out duration-200"
        x-transition:enter-start="{{ $panelOffscreen }}"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transform transition ease-in duration-150"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="{{ $panelOffscreen }}"
        class="pg-filter-flyout {{ theme('filter.flyout.panel') }} {{ $panelSide }}"
    >
        <div class="pg-filter-flyout-header {{ theme('filter.flyout.header') }}">
            <span
                id="pg-filter-flyout-title-{{ $tableName }}"
                class="{{ theme('filter.flyout.title') }}"
            >
                {{ trans('livewire-powergrid::datatable.buttons.filter') }}
            </span>

            <button
                type="button"
                data-cy="filter-flyout-close"
                x-on:click="$wire.showFilters = false"
                aria-label="{{ trans('livewire-powergrid::datatable.buttons.close') }}"
                class="{{ theme('filter.flyout.close') }}"
            >
                <x-livewire-powergrid::icons.x class="w-5 h-5" />
            </button>
        </div>

        <div class="pg-filter-flyout-body {{ theme('filter.flyout.body') }}">
            @include('livewire-powergrid::components.themes.tailwind.filters.fields', [
                'theme' => $theme,
                'tableName' => $tableName,
                'filtersFromColumns' => $filtersFromColumns,
                'gridClass' => 'grid grid-cols-1 gap-4',
                '__partial' => $__partial,
            ])
        </div>

        <div class="pg-filter-flyout-footer {{ theme('filter.flyout.footer') }}">
            <button
                type="button"
                data-cy="filter-flyout-clear-all"
                wire:click.prevent="clearAllFilters"
                class="{{ theme('filter.flyout.clear_all') }}"
            >
                {{ trans('livewire-powergrid::datatable.buttons.clear_all_filters') }}
            </button>
        </div>
    </div>
</div>
