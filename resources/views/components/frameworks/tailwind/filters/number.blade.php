@props([
    'inline' => null,
    'filter' => null,
    'column' => '',
])
@php

    $fieldClassName = data_get($filter, 'className');

    $field = data_get($filter, 'field');

    $componentAttributes = (array) data_get($filter, 'attributes');

    $defaultAttributes = $fieldClassName::getWireAttributes(
        $field,
        array_merge($filter, ['title' => data_get($column, 'title'), 'placeholder' => data_get($column, 'placeholder')])
    );

    $filterClasses = theme_style($theme, 'filterNumber.input');

    $placeholder = data_get($filter, 'placeholder');

    $params = array_merge([...data_get($filter, 'attributes'), ...$defaultAttributes, $filterClasses], $filter);
@endphp

@if ($params['component'])
    @unset($params['attributes'])

    <x-dynamic-component
        :component="$params['component']"
        :attributes="new \Illuminate\View\ComponentAttributeBag($params)"
    />
@else
    <div @class([
        'space-y-1' => !$inline,
        theme_style($theme, 'filterNumber.base')
    ])>
        @if (!$inline)
            <label class="block text-sm font-semibold text-pg-primary-700 dark:text-pg-primary-300">
                {{ $title }}
            </label>
        @endif
        <div @class([
            'w-full space-y-2 sm:flex gap-3 sm:space-y-0' => !$inline,
            'flex flex-col space-y-1.5' => $inline,
        ])>
            <div @class(['pl-0 w-full sm:w-1/2' => !$inline])>
                <input
                    {{ $defaultAttributes['inputStartAttributes'] }}
                    style="{{ data_get($column, 'headerStyle') }}"
                    type="text"
                    class="{{ $filterClasses }}"
                    placeholder="{{ $placeholder['min'] ?? __('Min') }}"
                >
            </div>
            <div @class(['pl-0 w-full sm:w-1/2' => !$inline, 'mt-1' => $inline])>
                <input
                    {{ $defaultAttributes['inputEndAttributes'] }}
                    @if ($inline) style="{{ data_get($column, 'headerStyle') }}" @endif
                    type="text"
                    class="{{ $filterClasses }}"
                    placeholder="{{ $placeholder['max'] ?? __('Max') }}"
                >
            </div>
        </div>
    </div>
@endif
