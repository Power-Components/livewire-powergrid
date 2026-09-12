@props([
    'column' => null,
    'class' => '',
    'inline' => null,
    'filter' => null,
])
@php
    $title = data_get($column, 'title');
    $trueLabel = data_get($filter, 'trueLabel');
    $falseLabel = data_get($filter, 'falseLabel');
    $selectClasses = Arr::toCssClasses([
        theme('filter.boolean.select'),
        $class,
    ]);
    $params = array_merge((array) data_get($filter, 'attributes', []), $filter);
@endphp

@if ($params['component'])
    @unset($params['attributes'])

    <x-dynamic-component
        :component="$params['component']"
        :attributes="new \Illuminate\View\ComponentAttributeBag($params)"
    />
@else
    <div
        @class([theme('filter.boolean.base'), 'space-y-1' => !$inline])
    >
        @if (!$inline)
            <label class="{{ theme('filter.label', 'block text-sm font-semibold text-zinc-700 dark:text-zinc-300') }}">
                {{ $title }}
            </label>
        @endif
        <div class="relative">
            <select
                class="{{ $selectClasses }}"
                style="{{ data_get($column, 'headerStyle') }}"
                {{ data_get($filter, 'selectAttributes') }}
            >
                <option value="all">{{ trans('livewire-powergrid::datatable.boolean_filter.all') }}</option>
                <option value="true">{{ $trueLabel }}</option>
                <option value="false">{{ $falseLabel }}</option>
            </select>
        </div>
    </div>
@endif
