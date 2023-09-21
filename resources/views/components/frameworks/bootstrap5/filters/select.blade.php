@props([
    'theme' => '',
    'class' => '',
    'column' => null,
    'inline' => null,
    'filter' => null,
])

@php
    $field = strval(data_get($filter, 'field'));
    $title = strval(data_get($filter, 'title'));
    
    $defaultAttributes = \PowerComponents\LivewirePowerGrid\Components\Filters\FilterSelect::getWireAttributes($field, $title);
    
    $filterClasses = Arr::toCssClasses([$theme->selectClass, $class, data_get($column, 'headerClass'), 'power_grid']);
    
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
