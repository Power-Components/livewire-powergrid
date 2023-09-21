@props([
    'theme' => '',
    'inline' => null,
    'filter' => null,
    'column' => '',
])

@php
    extract($filter);
    unset($filter['className']);
    
    $defaultAttributes = \PowerComponents\LivewirePowerGrid\Components\Filters\FilterNumber::getWireAttributes($field, $filter);
    
    $filterClasses = Arr::toCssClasses([$theme->inputClass, data_get($column, 'headerClass'), 'power_grid']);
    
    $params = array_merge([...data_get($filter, 'attributes'), ...$defaultAttributes, $filterClasses], $filter);
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
        <div>
            <input
                {{ $defaultAttributes['inputStartAttributes'] }}
                @if ($inline) style="{{ $theme->inputStyle }} {{ data_get($column, 'headerStyle') }}" @endif
                type="text"
                class="{{ $filterClasses }}"
                placeholder="{{ $placeholder['min'] ?? __('Min') }}"
            >
        </div>
        <div class="mt-1">
            <input
                {{ $defaultAttributes['inputEndAttributes'] }}
                @if ($inline) style="{{ $theme->inputStyle }} {{ data_get($column, 'headerStyle') }}" @endif
                type="text"
                class="{{ $filterClasses }}"
                placeholder="{{ $placeholder['max'] ?? __('Max') }}"
            >
        </div>
    </div>
@endif
