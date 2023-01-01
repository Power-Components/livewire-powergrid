@props([
    'theme' => '',
    'column' => null,
    'class' => '',
    'inline' => null,
    'filter' => null,
])
@php
    unset($filter['className']);
    extract($filter);
    $selectClasses = Arr::toCssClasses([
        $theme->selectClass, $class, data_get($column, 'headerClass'), 'power_grid'
    ])
@endphp
<div @class(['p-2' => !$inline,$theme->baseClass]) style="{{ $theme->baseStyle }}">
    @if(!$inline)
        <label class="text-gray-700 dark:text-gray-300"
               for="input_boolean_filter_{{ $field }}">
            {{ $title }}
        </label>
    @endif
    <div class="relative">
        <select id="input_boolean_filter_{{ $field }}"
                class="{{ $selectClasses }}"
                style="{{ data_get($column, 'headerStyle') }}"
                wire:input.lazy="filterBoolean('{{ $field }}', $event.target.value, '{{ $title }}')"
                wire:model="filters.boolean.{{ $field }}">
            <option value="all">{{ trans('livewire-powergrid::datatable.boolean_filter.all') }}</option>
            <option value="true">{{ $trueLabel }}</option>
            <option value="false">{{ $falseLabel }}</option>
        </select>
        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-pg-primary-700">
            <x-livewire-powergrid::icons.down class="w-4 h-4 dark:text-gray-300"/>
        </div>
    </div>
</div>
