@props([
    'class' => '',
    'column' => null,
    'inline' => null,
    'filter' => null,
    'options' => [],
])
@php
    $field = data_get($filter, 'field');
    $title = data_get($column, 'title');

    $defaultAttributes = \PowerComponents\LivewirePowerGrid\Components\Filters\FilterSelect::getWireAttributes(
        $field,
        $title,
    );

    $filterClasses = \Illuminate\Support\Arr::toCssClasses([
       theme_style($theme, 'filterSelect.select'),
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
        @class([theme_style($theme, 'filterSelect.base'), 'space-y-1' => !$inline])
    >
        @if (!$inline)
            <label class="block text-sm font-semibold text-pg-primary-700 dark:text-pg-primary-300">
                {{ $title }}
            </label>
        @endif
        <div class="relative">
            <select
                class="{{ $filterClasses }}"
                style="{{ data_get($column, 'headerStyle') }}"
                {{ $defaultAttributes['selectAttributes'] }}
            >
                @if(!data_get($params, 'params.disableOptionAll', false))
                    <option value="">{{ trans('livewire-powergrid::datatable.select.all') }}</option>
                @endif

                @php
                    $computedDatasource = data_get($filter, 'computedDatasource');
                    $dataSource = filled($computedDatasource)
                        ? $this->{$computedDatasource}
                        : data_get($filter, 'dataSource');
                @endphp

                @foreach ($dataSource as $key => $item)
                    <option
                        wire:key="select-{{ $tableName }}-{{ $key }}"
                        value="{{ $item[data_get($filter, 'optionValue')] }}"
                    >
                        {{ $item[data_get($filter, 'optionLabel')] }}
                    </option>
                @endforeach
            </select>
            <div
                class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-pg-primary-700 dark:text-pg-primary-300">
                <x-livewire-powergrid::icons.down class="w-4 h-4 dark:text-gray-300" />
            </div>
        </div>
    </div>
@endif
