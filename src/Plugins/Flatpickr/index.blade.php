@props([
    'inline' => null,
    'date' => null,
    'column' => null,
    'tableName' => null,
    'type' => 'datetime',
    'filter' => null,
])

@php
    $__partial = $__partial ?? $this;
    $deferred = $__partial->usesFilterPanel();
    $filtersProperty = $deferred ? 'draftFilters' : 'filters';
    $params = data_get($filter, 'params');
    $field = data_get($filter, 'field');
    $keyField = $deferred ? \PowerComponents\LivewirePowerGrid\Support\FilterKey::encode(strval($field)) : $field;
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
        'keyField' => $keyField,
        'tableName' => $tableName,
        'deferred' => $deferred,
        'filtersProperty' => $filtersProperty,
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
    x-data="pgFlatpickr"
    data-pg-params="{{ json_encode($params) }}"
>
    <div
        @class([theme('filter.date_picker.base'), 'space-y-1' => !$inline])
    >
        @if (!$inline)
            <label class="{{ theme('filter.label', 'block text-sm font-semibold text-zinc-700 dark:text-zinc-300') }}">
                {{ $title }}
            </label>
        @endif
        <form autocomplete="off">
            <input
                id="input_{{ $field }}"
                x-ref="rangeInput"
                wire:model="{{ $filtersProperty }}.{{ $type }}.{{ $keyField }}.formatted"
                @if ($deferred) data-pg-draft="{{ $type }}.{{ $keyField }}.formatted" @endif
                autocomplete="off"
                data-field="{{ $field }}"
                class="{{ theme('filter.date_picker.input') }} {{ data_get($column, 'headerClass') }}"
                type="text"
                readonly
                placeholder="{{ trans('livewire-powergrid::datatable.placeholders.select') }}"
            >
        </form>
    </div>
</div>
