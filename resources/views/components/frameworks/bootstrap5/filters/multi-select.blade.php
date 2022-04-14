@props([
    'theme' => '',
    'inline' => null,
    'multiSelect' => null,
    'column' => null,
    'tableName' => null,
])
<div x-cloak
     wire:ignore
     x-data="pgMultiSelectBs5({
        tableName: '{{ $tableName }}',
        dataField: '{{ $multiSelect['dataField'] }}',
    })">
    @if(filled($multiSelect))
        <div class="{{ $theme->baseClass }}" style="{{ $theme->baseStyle }}">
            <select data-none-selected-text="{{ trans('livewire-powergrid::datatable.multi_select.select') }}"
                    multiple
                    wire:model="filters.multi_select.{{ $multiSelect['dataField'] }}.values"
                    x-ref="select_picker_{{ $multiSelect['dataField'] }}"
                    data-live-search="true">
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
