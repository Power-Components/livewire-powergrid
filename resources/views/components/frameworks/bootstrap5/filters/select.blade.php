@props([
    'theme' => '',
    'class' => '',
    'column' => null,
    'inline' => null,
    'filter' => null,
])

@php
    $field = data_get($filter, 'field');
    $title = data_get($column, 'title');

    $defaultAttributes = \PowerComponents\LivewirePowerGrid\Components\Filters\FilterSelect::getWireAttributes($field, $title);

    $filterClasses = Arr::toCssClasses([data_get($theme, 'selectClass'), $class, data_get($column, 'headerClass'), 'power_grid']);

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
            class="{{ $filterClasses }}"
            style="{{ data_get($column, 'headerStyle') }}"
            {{ $defaultAttributes['selectAttributes'] }}
        >
            <option value="">{{ trans('livewire-powergrid::datatable.select.all') }}</option>
            @foreach (data_get($filter, 'dataSource') as $key => $item)
                <option
                    wire:key="select-{{ $tableName }}-{{ $key }}"
                    value="{{ $item[data_get($filter, 'optionValue')] }}"
                >
                    {{ $item[data_get($filter, 'optionLabel')] }}
                </option>
            @endforeach
        </select>
    </div>
@endif
