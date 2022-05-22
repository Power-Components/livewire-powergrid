@props([
    'theme' => '',
    'inline' => null,
    'number' => null,
    'column' => '',
])
<div>
    @if(filled($number))
        <div class="@if(!$inline) p-2 @endif">
            @if(!$inline)
                <label class="text-gray-700 dark:text-gray-300">{{ data_get($number, 'label') }}</label>
            @endif
            <div @class([
                'sm:flex w-full' => !$inline,
                'flex flex-col' => $inline,
                ])>
                <div @class([
                        'pl-0 pt-1 w-full sm:pr-3 sm:w-1/2' => !$inline,
                    ])>
                    <input
                        wire:model.debounce.800ms="filters.number.{{ data_get($number, 'dataField') }}.start"
                        wire:input.debounce.800ms="filterNumberStart('{{ data_get($number, 'dataField') }}', $event.target.value, '{{ data_get($number, 'label') }}')"
                        style="{{ $theme->inputStyle }} {{ data_get($column, 'headerStyle') }}"
                        type="text"
                        class="power_grid {{ $theme->inputClass }} {{ data_get($column, 'headerClass') }}"
                        placeholder="{{ __('Min') }}">
                </div>
                <div @class([
                        'pl-0 pt-1 w-full sm:w-1/2' => !$inline,
                        'mt-1' => $inline,
                    ])>
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
