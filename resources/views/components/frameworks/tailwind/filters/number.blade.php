@props([
    'theme' => '',
    'inline' => null,
    'number' => null,
    'column' => '',
])
<div>
    @if(filled($number))
        <div class="@if(!$inline) pt-2 p-2 @endif">
            @if(!$inline)
                <label class="text-gray-700 dark:text-gray-300">{{ data_get($number, 'label') }}</label>
            @endif
            <div class="@if($inline) flex flex-col @else  flex flex-row justify-between @endif">
                <div class="@if(!$inline) pl-0 pt-1 pr-3 @endif">
                    <input
                        wire:model.debounce.800ms="filters.number.{{ data_get($number, 'dataField') }}.start"
                        wire:input.debounce.800ms="filterNumberStart('{{ data_get($number, 'dataField') }}', $event.target.value, '{{ data_get($number, 'label') }}')"
                        @if($inline) style="{{ $theme->inputStyle }} {{ data_get($column, 'headerStyle') }}" @endif
                        type="text"
                        class="power_grid {{ $theme->inputClass }} {{ data_get($column, 'headerClass') }}"
                        placeholder="{{ __('Min') }}">
                </div>
                <div class="mt-1 justify-between">
                    <input
                        wire:model.debounce.800ms="filters.number.{{ data_get($number, 'dataField') }}.end"
                        wire:input.debounce.800ms="filterNumberEnd('{{ data_get($number, 'dataField') }}',$event.target.value, '{{ data_get($number, 'label') }}')"
                        @if($inline) style="{{ $theme->inputStyle }} {{ data_get($column, 'headerStyle') }}" @endif
                        type="text"
                        class="power_grid {{ $theme->inputClass }} {{ data_get($column, 'headerClass') }}"
                        placeholder="{{ __('Max') }}">
                </div>
            </div>
        </div>
    @endif
</div>
