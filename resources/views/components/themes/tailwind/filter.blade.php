@props([
    'theme' => null,
    'tableName' => null,
    'filtersFromColumns' => null,
    '__partial' => null,
    'openOnLoad' => false,
])

@php
    $__partial = $__partial ?? (isset($this) ? $this : null);
    $tableName = $tableName ?? $__partial->tableName;

    $element = $__partial->headerElement('filters');
    $count = $__partial->activeFilterCount();

    $columns = $__partial->filterPanelColumns();
    $gridClass = match ($columns) {
        3 => 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4',
        2 => 'grid grid-cols-1 md:grid-cols-2 gap-4',
        default => 'grid grid-cols-1 gap-4',
    };
    $panelWidth = match ($columns) {
        3 => 'lg:w-[48rem]',
        2 => 'lg:w-[36rem]',
        default => 'lg:w-96',
    };
@endphp

{{--
    Alpine `open` is local UI state. Livewire snapshots Alpine *before* a
    wire:click handler runs, so Apply/Clear must close first and then call
    $wire — otherwise the morph restores open=true and the panel stays up.
    Portaled widgets (flatpickr, tom-select, slim-select) render on <body>,
    so click.outside has to ignore them or the panel closes mid-interaction.
--}}
<div
    x-data="{
        open: {{ $openOnLoad ? 'true' : 'false' }},
        async toggle() {
            if (! this.$wire.filterPanelLoaded) {
                await this.$wire.loadFilterPanel()
                this.open = true
                return
            }
            this.open = ! this.open
        },
        closeOnOutside(event) {
            const target = event.target;
            if (! (target instanceof Element)) {
                this.open = false;
                return;
            }
            if (target.closest('.flatpickr-calendar, .ts-dropdown, .ss-content')) {
                return;
            }
            this.open = false;
        },
        apply() {
            this.open = false;
            this.$wire.applyFilters();
        },
        clearAll() {
            this.open = false;
            this.$wire.clearAllFilters();
        },
    }"
    wire:partial="pg-filters-{{ $tableName }}"
    wire:key="pg-filter-dropdown-{{ $tableName }}"
    class="{{ theme('filter.dropdown.wrapper') }}"
>
    <button
        type="button"
        x-on:click="toggle()"
        data-cy="filter-dropdown-trigger"
        title="{{ $element['title'] }}"
        aria-label="{{ $element['title'] }}"
        class="{{ theme('filter.dropdown.trigger', theme('header.filters.button')) }}"
    >
        {!! $element['iconHtml'] !!}
        @if ($count)
            <span
                data-cy="filter-dropdown-badge"
                class="{{ theme('filter.dropdown.badge') }}"
            >{{ $count }}</span>
        @endif
    </button>

    <div
        x-show="open"
        x-cloak
        style="display: none"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 -translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-1"
        x-on:click.outside="closeOnOutside($event)"
        x-on:keydown.escape.window="open = false"
        role="dialog"
        aria-modal="true"
        data-cy="filter-dropdown-panel"
        class="{{ theme('filter.dropdown.panel') }} {{ $panelWidth }}"
    >
        <div class="{{ theme('filter.dropdown.header') }}">
            <span class="{{ theme('filter.dropdown.title') }}">
                {{ trans('livewire-powergrid::datatable.buttons.filters_title') }}
            </span>

            <button
                type="button"
                data-cy="filter-dropdown-reset"
                wire:click.prevent="resetFilters"
                class="{{ theme('filter.dropdown.reset') }}"
            >
                {{ trans('livewire-powergrid::datatable.buttons.reset_filters') }}
            </button>
        </div>

        <div class="{{ theme('filter.dropdown.body') }}">
            @include('livewire-powergrid::components.themes.tailwind.filters.fields', [
                'theme' => $theme,
                'tableName' => $tableName,
                'filtersFromColumns' => $filtersFromColumns ?? null,
                'gridClass' => $gridClass,
                '__partial' => $__partial,
            ])
        </div>

        <div class="{{ theme('filter.dropdown.footer') }}">
            <button
                type="button"
                data-cy="filter-dropdown-clear"
                x-on:click="clearAll()"
                class="{{ theme('filter.dropdown.clear') }}"
            >
                {{ trans('livewire-powergrid::datatable.buttons.clear_all_filters') }}
            </button>

            <button
                type="button"
                data-cy="filter-dropdown-apply"
                x-on:click="apply()"
                class="{{ theme('filter.dropdown.apply') }}"
            >
                {{ trans('livewire-powergrid::datatable.buttons.apply_filters') }}
            </button>
        </div>
    </div>
</div>
