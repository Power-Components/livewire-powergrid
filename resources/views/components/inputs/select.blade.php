@props([
    'inline' => true,
    'filter' => null,
    'tableName' => null,
    'multiple' => true,
    'initialValues' => [],
    'options' => [],
    'title' => '',
    'theme' => null,
])

@php
    $framework = config('livewire-powergrid.plugins.select');
    $collection = collect();

    if (filled(data_get($filter, 'dataSource'))) {
        $collection = collect(data_get($filter, 'dataSource'))
            ->transform(function (array|\Illuminate\Support\Collection|\Illuminate\Database\Eloquent\Model $entry) use ($filter) {
                if (is_array($entry)) {
                    $entry = collect($entry);
                }
            return $entry->only([data_get($filter, 'optionValue'), data_get($filter, 'optionLabel')]);
        });
    } elseif (filled(data_get($filter, 'computedDatasource'))) {
        $collection = collect(data_get($filter, 'computedDatasource'))
            ->transform(function (array|\Illuminate\Support\Collection|\Illuminate\Database\Eloquent\Model $entry) use ($filter) {
                if (is_array($entry)) {
                    $entry = collect($entry);
                }
            return $entry->only([data_get($filter, 'optionValue'), data_get($filter, 'optionLabel')]);
        });
    }

    $params = [
        'tableName' => $tableName,
        'label' => $title,
        'dataField' => data_get($filter, 'field'),
        'optionValue' => data_get($filter, 'optionValue'),
        'optionLabel' => data_get($filter, 'optionLabel'),
        'options' => data_get($filter, 'params'),
        'initialValues' => $initialValues,
        'framework' => $framework[config('livewire-powergrid.plugins.select.default')],
    ];

    if (\Illuminate\Support\Arr::has($filter, ['url', 'method'])) {
        $params['asyncData'] = [
            'url' => data_get($filter, 'url'),
            'method' => data_get($filter, 'method'),
            'parameters' => data_get($filter, 'parameters'),
            'headers' => data_get($filter, 'headers'),
        ];
    }

    $alpineData = $framework['default'] == 'tom' ? 'pgTomSelect(' . \Illuminate\Support\Js::from($params) . ')' : 'pgSlimSelect(' . \Illuminate\Support\Js::from($params) . ')';
@endphp
<div
    x-cloak
    wire:ignore
    x-data="{{ $alpineData }}"
>
    @if (filled($filter))
        <div
            class="{{ theme_style($theme, 'filterSelect.base') }}"
        >
            @if (!$inline)
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">
                    {{ $title }}
                </label>
            @endif
            <select
                @if ($multiple) multiple @endif
                class="{{ theme_style($theme, 'filterSelect.select') }}"
                wire:model="filters.multi_select.{{ data_get($filter, 'field') }}.values"
                x-ref="select_picker_{{ data_get($filter, 'field') }}_{{ $tableName }}"
            >
                @if (!data_get($params, 'options.disableOptionAll', false))
                    <option value="">{{ trans('livewire-powergrid::datatable.multi_select.all') }}</option>
                @endif

                @if (blank(data_get($params, 'asyncData', [])))
                    @foreach ($collection->toArray() as $item)
                        <option wire:key="multi-select-option-{{ $loop->index }}"
                                value="{{ data_get($item, data_get($filter, 'optionValue')) }}">
                            {{ data_get($item, data_get($filter, 'optionLabel')) }}
                        </option>
                    @endforeach
                @endif
            </select>
        </div>
    @endif
</div>
