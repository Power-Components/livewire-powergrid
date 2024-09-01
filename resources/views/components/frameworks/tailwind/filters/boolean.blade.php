@props([
    'column' => null,
    'class' => '',
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

    $selectClasses = Arr::toCssClasses([
        theme_style($theme, 'filterBoolean.select'),
        $class,
    ]);

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
        @class([theme_style($theme, 'filterBoolean.base'), 'space-y-1' => !$inline])
    >
        @if (!$inline)
            <label class="block text-sm font-semibold text-pg-primary-700 dark:text-pg-primary-300">
                {{ $title }}
            </label>
        @endif
        <div class="relative">
            <select
                class="{{ $selectClasses }}"
                style="{{ data_get($column, 'headerStyle') }}"
                {{ $defaultAttributes['selectAttributes'] }}
            >
                <option value="all">{{ trans('livewire-powergrid::datatable.boolean_filter.all') }}</option>
                <option value="true">{{ $trueLabel }}</option>
                <option value="false">{{ $falseLabel }}</option>
            </select>
            <div
                class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-pg-primary-700 dark:text-pg-primary-300">
                <x-livewire-powergrid::icons.down class="w-4 h-4 dark:text-gray-300" />
            </div>
        </div>
    </div>
@endif
