@props([
    'theme' => '',
    'inline' => null,
    'filter' => null,
    'column' => '',
])
@php
    $fieldClassName = data_get($filter, 'className');
    $field = data_get($filter, 'field');

    $componentAttributes = (array) data_get($filter, 'attributes');

    $defaultAttributes = $fieldClassName::getWireAttributes($field, array_merge($filter, (array)$column));

    $filterClasses = Arr::toCssClasses([data_get($theme, 'inputClass'), data_get($column, 'headerClass'), 'power_grid']);

    $params = array_merge([...data_get($filter, 'attributes'), ...$defaultAttributes, $filterClasses], $filter);
@endphp

@if ($params['component'])
    @unset($params['attributes'])

    <x-dynamic-component
        :component="$params['component']"
        :attributes="new \Illuminate\View\ComponentAttributeBag($params)"
    />
@else
    <div>
        @if (!$inline)
            <label class="block text-sm font-medium text-pg-primary-700 dark:text-pg-primary-300">
                {{ $title }}
            </label>
        @endif
        <div @class([
            'sm:flex gap-3 w-full' => !$inline,
            'flex flex-col' => $inline,
        ])>
            <div @class(['pl-0 pt-1 w-full sm:w-1/2' => !$inline])>
                <input
                    {{ $defaultAttributes['inputStartAttributes'] }}
                    style="{{ data_get($theme, 'inputStyle') }} {{ data_get($column, 'headerStyle') }}"
                    type="text"
                    class="{{ $filterClasses }}"
                    placeholder="{{ $placeholder['min'] ?? __('Min') }}"
                >
            </div>
            <div @class(['pl-0 pt-1 w-full sm:w-1/2' => !$inline, 'mt-1' => $inline])>
                <input
                    {{ $defaultAttributes['inputEndAttributes'] }}
                    @if ($inline) style="{{ data_get($theme, 'inputStyle') }} {{ data_get($column, 'headerStyle') }}" @endif
                    type="text"
                    class="{{ $filterClasses }}"
                    placeholder="{{ $placeholder['max'] ?? __('Max') }}"
                >
            </div>
        </div>
    </div>
@endif
