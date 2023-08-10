@props([
    'theme' => '',
    'column' => null,
    'inline' => null,
    'filter' => null,
])
@php
    unset($filter['className']);
    extract($filter);
    
    $defaultAttributes = \PowerComponents\LivewirePowerGrid\Components\Filters\FilterBoolean::getWireAttributes($field, $title);
    
    $selectClasses = Arr::toCssClasses([$theme->selectClass, data_get($column, 'headerClass'), 'power_grid']);
    
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
        class="{{ $theme->baseClass }}"
        style="{{ $theme->baseStyle }}"
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
