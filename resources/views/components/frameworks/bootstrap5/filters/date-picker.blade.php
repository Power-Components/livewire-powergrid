@props([
    'theme' => '',
    'inline' => null,
    'date' => null,
    'column' => null,
    'tableName' => null,
    'type' => 'datetime',
])
@php
    $params = data_get($filter, 'params');
    $field = data_get($filter, 'field');
    $title = data_get($column, 'title');

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
        'label' => $title,
        'locale' => config('livewire-powergrid.plugins.flatpickr.locales.' . app()->getLocale()),
        'onlyFuture' => data_get($customConfig, 'only_future', false),
        'noWeekEnds' => data_get($customConfig, 'no_weekends', false),
        'customConfig' => $customConfig,
    ];
@endphp
<div
    wire:ignore.self
    x-data="pgFlatpickr(@js($params))"
>
    <div
        class="{{ data_get($theme, 'baseClass') }}"
        style="{{ data_get($theme, 'baseStyle') }}"
    >
        <form autocomplete="off">
            <input
                id="input_{{ $field }}"
                x-ref="rangeInput"
                wire:model="filters.{{ $type }}.{{ $field }}.formatted"
                autocomplete="off"
                data-field="{{ $field }}"
                style="{{ data_get($theme, 'inputStyle') }} {{ data_get($column, 'headerStyle') }}"
                class="power_grid {{ data_get($theme, 'inputClass') }} {{ data_get($column, 'headerClass') }}"
                type="text"
                readonly
                placeholder="{{ trans('livewire-powergrid::datatable.placeholders.select') }}"
            />
        </form>
    </div>
</div>
