@props([
    'theme' => '',
    'inline' => null,
    'multiSelect' => null,
    'column' => null,
    'tableName' => null,
])

@php
    $initialValues = data_get($filters['multi_select'], $multiSelect['dataField'], []);

    $defaultFramework = config('livewire-powergrid.plugins.multiselect.default');

    $params = [
        'tableName' => $tableName,
        'dataField' =>  $multiSelect['dataField'],
        'initialValues' => $initialValues,
        'defaultFramework' => (array) config('livewire-powergrid.plugins.multiselect.' . $defaultFramework)
    ];
@endphp
<div x-cloak
     wire:ignore
     x-data="pgMultiSelectBs5(@js($params))">
    @if(filled($multiSelect))
        <div class="{{ $theme->baseClass }}" style="{{ $theme->baseStyle }}">
            <select multiple
                    wire:model.defer="filters.multi_select.{{ $multiSelect['dataField'] }}.values"
                    x-ref="select_picker_{{ $multiSelect['dataField'] }}"
                    >
                <option value="">{{ trans('livewire-powergrid::datatable.multi_select.all') }}</option>
                @foreach(data_get($multiSelect, 'data_source') as $relation)
                    @php
                        $key = isset($relation['id']) ? 'id' : 'value';
                        if (isset($relation[$multiSelect['dataField']])) $key = $multiSelect['dataField'];
                    @endphp
                    <option value="{{ data_get($relation, $key) }}">
                        {{ $relation[data_get($multiSelect, 'text')] }}
                    </option>
                @endforeach
            </select>
        </div>
    @endif
</div>
