@props([
    'theme' => '',
    'inline' => null,
    'date' => null,
    'column' => null,
    'tableName' => null,
])
@php
    $tableName = \Illuminate\Support\Str::kebab($tableName);
    $customConfig = [];
    if (data_get($date, 'config')) {
        foreach (data_get($date, 'config') as $key => $value) {
            $customConfig[$key] = $value;
        }
    }
@endphp
<div wire:ignore x-data="pgFlatPickr({
        dataField: '{{ $date['dataField'] }}',
        tableName: '{{ $tableName }}',
        filterKey: 'enabledFilters.date_picker.{{ $date['dataField'] }}',
        label: '{{ $date['label'] }}',
        locale: {{ json_encode(config('livewire-powergrid.plugins.flatpickr.locales.'.app()->getLocale())) }},
        onlyFuture: {{ json_encode(data_get($customConfig, 'only_future', false)) }},
        noWeekEnds: {{ json_encode(data_get($customConfig, 'no_weekends', false)) }},
        customConfig: {{ json_encode($customConfig) }}
    })">
    <div class="{{ $theme->baseClass }}" style="{{ $theme->baseStyle }}">
        @if(!$inline)
            <label for="input_{{ data_get($date, 'field') }}"
                   class="text-gray-700 dark:text-gray-300">
                {{ data_get($date, 'label') }}
            </label>
        @endif
        <form autocomplete="off">
            <input id="input_{{ data_get($date, 'field') }}"
                   x-ref="rangeInput"
                   autocomplete="off"
                   data-field="{{ data_get($date, 'dataField') }}"
                   style="{{ $theme->inputStyle }} {{ data_get($column, 'headerStyle') }}"
                   class="power_grid {{ $theme->inputClass }} {{ data_get($column, 'headerClass') }}"
                   type="text"
                   readonly
                   placeholder="{{ trans('livewire-powergrid::datatable.placeholders.select') }}"
                   wire:model="filters.input_date_picker.{{ data_get($date, 'dataField') }}">
        </form>
    </div>
</div>

