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
            $theme->baseClass,
            $booleanFilter['class'] => $booleanFilter['class'] != ''
        ]) style="{{ $theme->baseStyle }}">
            <select id="input_boolean_filter_{{ data_get($booleanFilter, 'field') }}"
                    style="{{ data_get($column, 'headerStyle') }}"
                    class="power_grid {{ $theme->selectClass }} {{ $class }} {{ data_get($column, 'headerClass') }}"
                    wire:input.lazy="filterBoolean('{{ $booleanFilter['dataField'] }}', $event.target.value, '{{ $booleanFilter['label'] }}')"
                    wire:model="filters.boolean.{{ $booleanFilter['dataField'] }}">
                <option value="all">{{ trans('livewire-powergrid::datatable.boolean_filter.all') }}</option>
                <option value="true">{{ data_get($booleanFilter, 'true_label') }}</option>
                <option value="false">{{ data_get($booleanFilter, 'false_label') }}</option>
            </select>
        </div>
    @endif
</div>
