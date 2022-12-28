@props([
    'theme' => '',
    'inline' => true,
    'multiSelect' => null,
    'tableName' => null,
    'multiple' => true,
    'initialValues' => [],
])

@php
    $framework = config('livewire-powergrid.plugins.multiselect');

    if (filled(data_get($multiSelect, 'dataSource'))) {
         $collection = collect($multiSelect['dataSource'])
         ->transform(function (array|\Illuminate\Support\Collection|\Illuminate\Database\Eloquent\Model $entry) use ($multiSelect) {
             if (is_array($entry)) {
                 $entry = collect($entry);
             }
             return $entry->only([$multiSelect['optionId'], $multiSelect['optionLabel']]);
         });
    }

    $params = [
        'tableName' => $tableName,
        'dataField' => $multiSelect['dataField'],
        'optionId' => $multiSelect['optionId'],
        'optionLabel' => $multiSelect['optionLabel'],
        'initialValues' => $initialValues,
        'framework' => $framework[config('livewire-powergrid.plugins.multiselect.default')]
    ];

    if (filled(data_get($multiSelect, 'asyncData'))) {
        $params['asyncData'] = data_get($multiSelect, 'asyncData');
    }

    $alpineData = $framework['default'] == 'tom' ?
        'pgTomSelect('.Js::from($params).')' :
        'pgSlimSelect('.Js::from($params).')';

@endphp
<div x-cloak
     wire:ignore
     x-data="{{ $alpineData }}">
    @if(filled($multiSelect))
        <div @class([
            'p-2' => !$inline,
            $theme->baseClass,
        ]) style="{{ $theme->baseStyle }}">
            @if(!$inline)
                <label class="text-gray-700 dark:text-gray-300">{{ data_get($multiSelect, 'label') }}</label>
            @endif
            <select @if ($multiple) multiple @endif
                    class="{{ $theme->selectClass }}"
                    wire:model.defer="filters.multi_select.{{ $multiSelect['dataField'] }}.values"
                    x-ref="select_picker_{{ $multiSelect['dataField'] }}_{{ $tableName }}"
            >
                <option value="">{{ trans('livewire-powergrid::datatable.multi_select.all') }}</option>
                @if(blank(data_get($params, 'asyncData', [])))
                    @foreach($collection->toArray() as $item)
                        <option value="{{ data_get($item, $multiSelect['optionId']) }}">
                            {{ data_get($item, $multiSelect['optionLabel'])  }}
                        </option>
                    @endforeach
                @endif
            </select>
        </div>
    @endif
</div>
