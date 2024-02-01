@props([
    'theme' => '',
    'column' => null,
    'inline' => null,
    'filter' => null,
])
@php
    $fieldClassName = data_get($filter, 'className');
    $field = data_get($filter, 'field');
    $title = data_get($column, 'title');

    $trueLabel = data_get($filter, 'trueLabel');
    $falseLabel = data_get($filter, 'falseLabel');

    $defaultAttributes = $fieldClassName::getWireAttributes($field, $title);

    $selectClasses = Arr::toCssClasses([data_get($theme, 'selectClass'), data_get($column, 'headerClass'), 'power_grid']);

    $params = array_merge([...data_get($filter, 'attributes'), ...$defaultAttributes], $filter);
@endphp

@if ($params['component'])
    @unset($params['attributes'])

    <x-dynamic-component
        :component="$params['component']"
        :attributes="new \Illuminate\View\ComponentAttributeBag($params)"
    />
@else
    <div
        class="{{ data_get($theme, 'baseClass') }}"
        style="{{ data_get($theme, 'baseStyle') }}"
    >
        <select
            style="{{ data_get($column, 'headerStyle') }}"
            class="{{ $selectClasses }}"
            {{ $defaultAttributes['selectAttributes'] }}
        >
            <option value="all">{{ trans('livewire-powergrid::datatable.boolean_filter.all') }}</option>
            <option value="true">{{ $trueLabel }}</option>
            <option value="false">{{ $falseLabel }}</option>
        </select>
    </div>
@endif
