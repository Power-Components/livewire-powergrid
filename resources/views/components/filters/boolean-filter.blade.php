@props([
    'theme' => '',
    'column' => null,
    'class' => '',
    'inline' => null,
    'booleanFilter' => null,
])
<div>
    @if(filled($booleanFilter))
        <div class="@if($inline) {{ $theme->divClassInline }} @endif {{ $theme->divClassNotInline }}{!! ($booleanFilter['class'] != '') ?? '' !!}">
            @if(!$inline)
                <label class="text-gray-700 dark:text-gray-300"
                       for="input_boolean_filter_{{ data_get($booleanFilter, 'field') }}">
                    {{ data_get($booleanFilter, 'label') }}
                </label>
            @endif
            <div class="relative">
                <select id="input_boolean_filter_{{ data_get($booleanFilter, 'field') }}"
                        style="{{ data_get($column, 'headerStyle') }}"
                        class="power_grid {{ $theme->inputClass }} {{ $class }} {{ data_get($column, 'headerClass') }}"
                        wire:input.lazy="filterBoolean('{{ $booleanFilter['dataField'] }}', $event.target.value, '{{ $booleanFilter['label'] }}')">
                    <option value="all">{{ trans('livewire-powergrid::datatable.boolean_filter.all') }}</option>
                    <option value="true">{{ data_get($booleanFilter, 'true_label') }}</option>
                    <option value="false">{{ data_get($booleanFilter, 'false_label') }}</option>
                </select>
                <div class="{{ $theme->relativeDivClass }}">
                    <x-livewire-powergrid::icons.down class="w-4 h-4"/>
                </div>
            </div>
        </div>
    @endif
</div>
