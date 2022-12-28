@props([
    'theme' => '',
    'class' => '',
    'column' => null,
    'inline' => null,
    'select' => null
])
@php
    $selectClasses = Arr::toCssClasses([
        $theme->selectClass, $class, data_get($column, 'headerClass'), 'power_grid'
    ])
@endphp
<div>
    @if(filled($select))
        <div @class([
            'p-2' => !$inline,
            $theme->baseClass,
        ]) style="{{ $theme->baseStyle }}">
            @if(!$inline)
                <label for="input_{{ data_get($select, 'dataField') }}" class="text-pg-primary-700 dark:text-pg-primary-300">{{ data_get($select, 'label')  }}</label>
            @endif
                <div @class(['pt-1' => !$inline, 'relative'])>
                    <select class="{{ $selectClasses }}"
                            style="{{ data_get($column, 'headerStyle') }}"
                            wire:input.debounce.500ms="filterSelect('{{ data_get($select, 'dataField') }}','{{ data_get($select, 'label')  }}')"
                            wire:model.debounce.500ms="filters.select.{{ data_get($select, 'dataField')  }}">
                        <option value="">{{ trans('livewire-powergrid::datatable.select.all') }}</option>
                        @foreach(data_get($select, 'dataSource') as $relation)
                            @php
                                $key = isset($relation['id']) ? 'id' : 'value';
                                if (isset($relation[$select['dataField']])) $key = $select['dataField'];
                            @endphp
                            <option value="{{ data_get($relation, $key) }}">
                                {{ $relation[data_get($select, 'displayField') ] }}
                            </option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-pg-primary-700">
                        <x-livewire-powergrid::icons.down class="w-4 h-4 dark:text-gray-300"/>
                    </div>
                </div>
        </div>
    @endif
</div>
