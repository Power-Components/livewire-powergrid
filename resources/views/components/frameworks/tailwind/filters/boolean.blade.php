@props([
    'theme' => '',
    'column' => null,
    'class' => '',
    'inline' => null,
    'booleanFilter' => null,
])
<div>
    @if(filled($booleanFilter))
        <div @class([
            'pt-2 p-2' => !$inline,
            $theme->baseClass,
            $booleanFilter['class'] => $booleanFilter['class'] != ''
        ]) style="{{ $theme->baseStyle }}">
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
                        wire:input.lazy="filterBoolean('{{ $booleanFilter['dataField'] }}', $event.target.value, '{{ $booleanFilter['label'] }}')"
                        wire:model="filters.boolean.{{ $booleanFilter['dataField'] }}">
                    <option value="all">{{ trans('livewire-powergrid::datatable.boolean_filter.all') }}</option>
                    <option value="true">{{ data_get($booleanFilter, 'true_label') }}</option>
                    <option value="false">{{ data_get($booleanFilter, 'false_label') }}</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-700 dark:bg-slate-500 dark:text-slate-200 dark:placeholder-slate-200 dark:border-slate-500">
                    <x-livewire-powergrid::icons.down class="w-4 h-4"/>
                </div>
            </div>
        </div>
    @endif
</div>
