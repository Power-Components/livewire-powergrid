@props([
    'theme' => '',
    'inline' => true,
    'filter' => null,
    'tableName' => null,
    'multiple' => true,
    'initialValues' => [],
])

@php
    $framework  = config('livewire-powergrid.plugins.multiselect');
    $collection = collect();

    if (filled(data_get($filter, 'dataSource'))) {
         $collection = collect(data_get($filter, 'dataSource'))
         ->transform(function (array|\Illuminate\Support\Collection|\Illuminate\Database\Eloquent\Model $entry) use ($filter) {
             if (is_array($entry)) {
                 $entry = collect($entry);
             }
             return $entry->only([data_get($filter, 'optionValue'), data_get($filter, 'optionLabel')]);
         });
    }

    $params = [
        'tableName' => $tableName,
        'title' => data_get($filter, 'title'),
        'dataField' => data_get($filter, 'field'),
        'optionValue' => data_get($filter, 'optionValue'),
        'optionLabel' => data_get($filter, 'optionLabel'),
        'initialValues' => $initialValues,
        'framework' => $framework[config('livewire-powergrid.plugins.multiselect.default')]
    ];

    if (str(data_get($filter, 'className'))->contains('FilterMultiSelectAsync')) {
        $params['asyncData'] = [
             'url' => data_get($filter, 'url'),
             'method' => data_get($filter, 'method'),
             'parameters' => data_get($filter, 'parameters'),
        ];
    }

    $alpineData = $framework['default'] == 'tom' ?
        'pgTomSelect('.Js::from($params).')' :
        'pgSlimSelect('.Js::from($params).')';

@endphp
<div x-cloak
     wire:ignore
     x-data="{{ $alpineData }}">
    @if(filled($filter))
        <div @class([
            'p-2' => !$inline,
            $theme->baseClass,
        ]) style="{{ $theme->baseStyle }}">
            @if(!$inline)
                <label class="text-gray-700 dark:text-gray-300">{{ data_get($filter, 'title') }}</label>
            @endif
            <select @if ($multiple) multiple @endif
                    class="{{ $theme->selectClass }}"
                    wire:model.defer="filters.multi_select.{{ data_get($filter, 'field') }}.values"
                    x-ref="select_picker_{{ data_get($filter, 'field') }}_{{ $tableName }}"
            >
                <option value="">{{ trans('livewire-powergrid::datatable.multi_select.all') }}</option>
                @if(blank(data_get($params, 'asyncData', [])))
                    @foreach($collection->toArray() as $item)
                        <option value="{{ data_get($item, data_get($filter, 'optionValue')) }}">
                            {{ data_get($item, data_get($filter, 'optionLabel'))  }}
                        </option>
                    @endforeach
                @endif
            </select>
        </div>
    @endif
</div>
