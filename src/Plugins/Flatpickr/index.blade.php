@props([
    'inline' => null,
    'date' => null,
    'column' => null,
    'tableName' => null,
    'type' => 'datetime',
    'filter' => null,
])

@once
    @php
        $pluginDir = dirname((new \ReflectionClass(\PowerComponents\LivewirePowerGrid\Plugins\Flatpickr\FlatpickrPlugin::class))->getFileName());
        $jsContent = file_get_contents($pluginDir . '/index.js');
    @endphp
    <script>
        {!! $jsContent !!}
    </script>
@endonce

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
                wire:model="filters.{{ $type }}.{{ $field }}.formatted"
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
