@props([
    'theme' => '',
    'column' => null,
    'inline' => null,
    'filter' => null,
])
@php
    unset($filter['className']);
    extract($filter);

    $selectClasses = Arr::toCssClasses([$theme->selectClass, data_get($column, 'headerClass'), 'power_grid'])
@endphp
<div class="{{ $theme->baseClass }}" style="{{ $theme->baseStyle }}">
    <select id="input_boolean_filter_{{ $field }}"
            style="{{ data_get($column, 'headerStyle') }}"
            class="{{ $selectClasses }}"
            wire:input.lazy="filterBoolean('{{ $field }}', $event.target.value, '{{ $title }}')"
            wire:model="filters.boolean.{{ $field }}">
        <option value="all">{{ trans('livewire-powergrid::datatable.boolean_filter.all') }}</option>
        <option value="true">{{ $trueLabel }}</option>
        <option value="false">{{ $falseLabel }}</option>
    </select>
</div>
