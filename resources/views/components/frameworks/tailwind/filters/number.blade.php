@props([
    'theme' => '',
    'inline' => null,
    'filter' => null,
    'column' => '',
])
@php
    unset($filter['className']);
    extract($filter);

    $filterClasses = Arr::toCssClasses([
        $theme->inputClass, data_get($column, 'headerClass'), 'power_grid'
    ])
@endphp
<div class="@if(!$inline) p-2 @endif">
    @if(!$inline)
        <label class="text-gray-700 dark:text-gray-300">{{ $title }}</label>
    @endif
    <div @class(['sm:flex w-full' => !$inline,'flex flex-col' => $inline])>
        <div @class(['pl-0 pt-1 w-full sm:pr-3 sm:w-1/2' => !$inline])>
            <input
                wire:model.debounce.800ms="filters.number.{{ $field }}.start"
                wire:input.debounce.800ms="filterNumberStart(@js($filter), $event.target.value)"
                style="{{ $theme->inputStyle }} {{ data_get($column, 'headerStyle') }}"
                type="text"
                class="{{ $filterClasses }}"
                placeholder="{{ __('Min') }}">
        </div>
        <div @class(['pl-0 pt-1 w-full sm:w-1/2' => !$inline, 'mt-1' => $inline])>
            <input
                wire:model.debounce.800ms="filters.number.{{ $field }}.end"
                wire:input.debounce.800ms="filterNumberEnd(@js($filter), $event.target.value)"
                @if($inline) style="{{ $theme->inputStyle }} {{ data_get($column, 'headerStyle') }}" @endif
                type="text"
                class="{{ $filterClasses }}"
                placeholder="{{ __('Max') }}">
        </div>
    </div>
</div>
