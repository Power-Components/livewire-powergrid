@props([
    'theme' => '',
    'inline' => null,
    'filter' => null,
    'column' => '',
])
@php
    extract($filter);
    unset($filter['className']);

    $filterClasses = Arr::toCssClasses([
        $theme->inputClass, data_get($column, 'headerClass'), 'power_grid'
    ])
@endphp
<div class="{{ $theme->baseClass }}" style="{{ $theme->baseStyle }}">
    <div>
        <input
            data-id="{{ $field }}"
            wire:model.debounce.800ms="filters.number.{{ $field }}.start"
            wire:input.debounce.800ms="filterNumberStart(@js($filter), $event.target.value)"
            @if($inline) style="{{ $theme->inputStyle }} {{ data_get($column, 'headerStyle') }}" @endif
            type="text"
            class="{{ $filterClasses }}"
            placeholder="{{ __('Min') }}">
    </div>
    <div class="mt-1">
        <input
            data-id="{{ $field }}"
            wire:model.debounce.800ms="filters.number.{{ $field }}.end"
            wire:input.debounce.800ms="filterNumberEnd(@js($filter), $event.target.value)"
            @if($inline) style="{{ $theme->inputStyle }} {{ data_get($column, 'headerStyle') }}" @endif
            type="text"
            class="{{ $filterClasses }}"
            placeholder="{{ __('Max') }}">
    </div>
</div>
