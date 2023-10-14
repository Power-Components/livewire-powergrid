@props([
    'theme' => '',
    'inline' => null,
    'date' => null,
    'column' => null,
    'tableName' => null,
    'type' => 'datetime',
])
@php
    unset($filter['className']);
    extract($filter);

    $customConfig = [];
    if ($params) {
        foreach ($params as $key => $value) {
            $customConfig[$key] = $value;
        }
    }

    $params = [
        'type' => $type,
        'dataField' => $field,
        'tableName' => $tableName,
        'filterKey' => 'enabledFilters.datetime.' . $field,
        'label' => $title,
        'locale' => config('livewire-powergrid.plugins.flatpickr.locales.' . app()->getLocale()),
        'onlyFuture' => data_get($customConfig, 'only_future', false),
        'noWeekEnds' => data_get($customConfig, 'no_weekends', false),
        'customConfig' => $customConfig,
    ];
@endphp
<div
    wire:ignore
    x-data="pgFlatpickr(@js($params))"
>
    <div
        class="{{ $theme->baseClass }}"
        style="{{ $theme->baseStyle }}"
    >
        @if (!$inline)
            <label class="block text-sm font-medium text-pg-primary-700 dark:text-pg-primary-300">
                {{ $title }}
            </label>
        @endif
        <form autocomplete="off">
            <input
                id="input_{{ $column }}"
                x-ref="rangeInput"
                wire:model="filters.{{ $type }}.{{ $field }}.formatted"
                autocomplete="off"
                data-field="{{ $field }}"
                style="{{ $theme->inputStyle }} {{ data_get($column, 'headerStyle') }}"
                class="power_grid {{ $theme->inputClass }} {{ data_get($column, 'headerClass') }}"
                type="text"
                readonly
                placeholder="{{ trans('livewire-powergrid::datatable.placeholders.select') }}"
            >
        </form>
    </div>
</div>
