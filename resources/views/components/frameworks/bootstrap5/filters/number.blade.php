@props([
    'theme' => '',
    'inline' => null,
    'number' => null,
    'column' => '',
])
<div>
    @if(filled($number))
        <div class="{{ $theme->baseClass }}" style="{{ $theme->baseStyle }}">
            <div>
                <input
                    data-id="{{ data_get($number, 'field') }}"
                    wire:model.debounce.800ms="filters.number.{{ data_get($number, 'dataField') }}.start"
                    wire:input.debounce.800ms="filterNumberStart('{{ data_get($number, 'dataField') }}', $event.target.value,'{{ data_get($number, 'label') }}')"
                    @if($inline) style="{{ $theme->inputStyle }} {{ data_get($column, 'headerStyle') }}" @endif
                    type="number"
                    class="power_grid {{ $theme->inputClass }} {{ data_get($column, 'headerClass') }}"
                    placeholder="Min">
            </div>
            <div class="mt-1">
                <input
                    data-id="{{ $number['field'] }}"
                    wire:model.debounce.800ms="filters.number.{{ data_get($number, 'dataField') }}.end"
                    wire:input.debounce.800ms="filterNumberEnd('{{ data_get($number, 'dataField') }}',$event.target.value,'{{ data_get($number, 'label') }}')"
                    @if($inline) style="{{ $theme->inputStyle }} {{ data_get($column, 'headerStyle') }}" @endif
                    type="number"
                    class="power_grid {{ $theme->inputClass }} {{ data_get($column, 'headerClass') }}"
                    placeholder="Max">
            </div>
        </div>
    @endif
</div>
