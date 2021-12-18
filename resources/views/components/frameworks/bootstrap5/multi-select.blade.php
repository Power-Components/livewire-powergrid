@props([
    'theme' => '',
    'inline' => null,
    'multiSelect' => null,
    'column' => null
])
<div x-cloak
     x-data="pgMultiSelectBs5({
            dataField: '{{ $multiSelect['data_field'] }}',
    })">
    @if(filled($multiSelect))
        <div wire:ignore
             class="flex @if(!$inline) col-md-6 col-lg-3 @endif"
             style="max-width: 370px !important;">

            @if(!$inline)
                <label for="input_{{ data_get($multiSelect, 'data_field') }}">
                    {{ data_get($multiSelect, 'label') }}
                </label>
            @endif
            <select data-none-selected-text="{{ trans('livewire-powergrid::datatable.multi_select.select') }}"
                    multiple
                    x-ref="select_picker_{{ $multiSelect['data_field'] }}"
                    class="power_grid_select form-control active"
                    data-live-search="{{ data_get($multiSelect, 'live-search') }}">

                <option value="">{{ trans('livewire-powergrid::datatable.multi_select.all') }}</option>
                @foreach(data_get($multiSelect, 'data_source') as $relation)
                    <option value="{{ data_get($relation, 'id') }}">
                        {{ $relation[data_get($multiSelect, 'display_field')] }}
                    </option>
                @endforeach
            </select>
        </div>
    @endif
</div>
