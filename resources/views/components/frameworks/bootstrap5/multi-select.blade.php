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
        <div class="flex @if(!$inline) col-md-6 col-lg-3 @endif"
             style="max-width: 370px !important;">

            @if(!$inline)
                <label for="input_{{ data_get($multiSelect, 'dataField') }}">
                    {{ data_get($multiSelect, 'label') }}
                </label>
            @endif
            <select data-none-selected-text="{{ trans('livewire-powergrid::datatable.multi_select.select') }}"
                    multiple
                    wire:model="filters.multi_select.{{ $multiSelect['dataField'] }}.values"
                    x-ref="select_picker_{{ $multiSelect['dataField'] }}"
                    class="power_grid_select form-control shadow-none active"
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
