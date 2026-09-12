@props([
    'inline' => null,
    'filter' => null,
    'column' => '',
])
@php
    $title = data_get($column, 'title');
    $filterClasses = theme('filter.number.input');
    $placeholder = data_get($filter, 'placeholder');
    $params = array_merge((array) data_get($filter, 'attributes', []), $filter);
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
        theme('filter.number.base')
    ])>
        @if (!$inline)
            <label class="{{ theme('filter.label', 'block text-sm font-semibold text-zinc-700 dark:text-zinc-300') }}">
                {{ $title }}
            </label>
        @endif
        <div @class([
            'w-full space-y-2 sm:flex gap-3 sm:space-y-0' => !$inline,
            'flex flex-col space-y-1.5' => $inline,
        ])>
            <div @class(['pl-0 w-full sm:w-1/2' => !$inline])>
                <input
                    {{ data_get($filter, 'inputStartAttributes') }}
                    style="{{ data_get($column, 'headerStyle') }}"
                    type="text"
                    class="{{ $filterClasses }}"
                    placeholder="{{ $placeholder['min'] ?? __('Min') }}"
                >
            </div>
            <div @class(['pl-0 w-full sm:w-1/2' => !$inline, 'mt-1' => $inline])>
                <input
                    {{ data_get($filter, 'inputEndAttributes') }}
                    @if ($inline) style="{{ data_get($column, 'headerStyle') }}" @endif
                    type="text"
                    class="{{ $filterClasses }}"
                    placeholder="{{ $placeholder['max'] ?? __('Max') }}"
                >
            </div>
        </div>
    </div>
@endif
